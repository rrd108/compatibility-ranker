<?php
use PDO;

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  return;
}

require('./secrets.php');

if (isset($_GET['get-token'])) {
  require('./get-token.php');
  return;
}

if (!isset($_GET['token'])) {
  header('HTTP/1.0 401 Unauthorized');
  return;
}

if (isset($_GET['token'])) {
  $tokenFound = false;
  foreach ($secrets['users'] as $usr => $pass) {
    if ($_GET['token'] == md5($usr . strrev($pass))) {
      $tokenFound = true;
    }
  }
}

if (!$tokenFound) {
  header('HTTP/1.0 401 Unauthorized');
  return;
}


$pdo = new PDO('mysql:host=localhost;dbname=' . $secrets['mysqlDatabase'], $secrets['mysqlUser'], $secrets['mysqlPass']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


if (isset($_GET['names'])) {
  $stmt = $pdo->prepare("SELECT id, name, sex, YEAR(birth_date) AS birth_year, naksatra, moon
    FROM devs
    WHERE inactive = ''
    ORDER BY name");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($result);
  return;
}

if ($_GET['analysis']) {
  $zodiacs = ['Aires', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 'Sagittarius', 'Capricorn', 'Aquarius', 'Pisces'];

  $chart = file_get_contents('../data/chart.json');
  $chart = json_decode($chart);

  $stmt = $pdo->prepare("SELECT id, name, sex, YEAR(birth_date) AS birth_year, naksatra, moon
    FROM devs
    WHERE id = ?");
  $stmt->execute([$_GET['analysis']]);
  $person = $stmt->fetch(PDO::FETCH_ASSOC);

  // select everybody for the other sex
  $sex = in_array($person['sex'], ['nő', 'female']) ? 'girl' : 'boy';

  $oppositeSex = ($sex == 'girl') ? '("férfi", "male")' : '("nő", "female")';
  $stmt = $pdo->prepare("SELECT id, name, birth_date, (" . $person['birth_year'] . " - YEAR(birth_date)) AS age_difference, naksatra, moon
    FROM devs
    WHERE sex IN $oppositeSex
    AND inactive = ''");
  $stmt->execute();
  $possiblePartners = $stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($possiblePartners as $i => $possiblePartner) {
    $possiblePartners[$i]['points'] = -1;
    $possiblePartners[$i]['moonPosition'] = '?';

    if ($possiblePartner['naksatra'] && $possiblePartner['moon']) {
      // find the chart entry for this combination
      foreach ($chart as $points) {
        // points
        if ($sex == 'girl') {
          if ($points->girl == $person['naksatra'] && $points->boy == $possiblePartner['naksatra']) {
            $possiblePartners[$i]['points'] = $points->point;
          }
        }
        if ($sex == 'boy') {
          if ($points->girl == $possiblePartner['naksatra'] && $points->boy == $person['naksatra']) {
            $possiblePartners[$i]['points'] = $points->point;
          }
        }

        $personMoonPosition = array_search($person['moon'], $zodiacs);
        $possiblePartnerMoonPosition = array_search($possiblePartner['moon'], $zodiacs);
        if ($sex == 'girl') {
          $moonPositionDifference = $personMoonPosition - $possiblePartnerMoonPosition;
        }
        if ($sex == 'boy') {
          $moonPositionDifference =  $possiblePartnerMoonPosition - $personMoonPosition;
        }
        $possiblePartners[$i]['stridirgha'] = $moonPositionDifference > 0 ? $moonPositionDifference : 12 + $moonPositionDifference;
      }
    }
  }

  echo json_encode($possiblePartners);
  return;
}