<?php
namespace CompatibilityRanker;

use Branca\Branca;
use PDO;

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

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

if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
  header('HTTP/1.0 401 Unauthorized');
  return;
}

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
  $token = str_replace('ApiKey ', '', $_SERVER['HTTP_AUTHORIZATION']);

  $branca = new Branca($secrets['salt']);
  $userEmail = $branca->decode($token);

  $tokenFound = in_array($userEmail, array_keys($secrets['users']));
  if (!$tokenFound) {
    header('HTTP/1.0 401 Unauthorized');
    return;
  }
}

// user is authenticated

$pdo = new PDO('mysql:host=' . $secrets['mysql']['host'] . ';charset=utf8;dbname=' . $secrets['mysql']['database'], $secrets['mysql']['user'], $secrets['mysql']['pass']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $data = json_decode(file_get_contents('php://input'));
  $data->data->ranpass = substr(uniqid(), 0 ,6);
  $data->data->date = date('Y-m-d');
  $data = (array) $data->data;
  $stmt = $pdo->prepare("INSERT INTO devs (name, birth_date, birth_time, birth_place, sex, moon, naksatra, ranpass, date) VALUES (:name, :birth_date, :birth_time, :birth_place, :sex, :moon, :naksatra, :ranpass, :date)");
  $stmt->execute($data);
  $result = $pdo->lastInsertId();
  echo json_encode($result);
  return;
}

if (isset($_GET['names'])) {
  $stmt = $pdo->prepare("SELECT id, name, sex, YEAR(birth_date) AS birth_year, naksatra, moon, info
    FROM devs
    WHERE inactive = '' OR inactive IS NULL
    ORDER BY name");
  $stmt->execute();
  $id = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($id);
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
    AND (inactive = '' OR inactive IS NULL)");
  $stmt->execute();
  $possiblePartners = $stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($possiblePartners as $i => $possiblePartner) {
    $possiblePartners[$i]['points'] = -1;

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