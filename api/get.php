<?php

namespace CompatibilityRanker;

use PDO;

function tokenize($input)
{
  $input = str_replace(',', '', $input);
  $input = str_replace(' ', '', $input);
  $input = strtolower($input);
  return $input;
}

function isSameNaksatras($naksatra1, $naksatra2, $naksatraNames)
{
  if (tokenize($naksatra1) == tokenize($naksatra2)) {
    return true;
  }

  preg_match('/([a-zA-z ]*),?.*(\d)/', $naksatra1, $matches);
  $naksatra1 = isset($matches[1]) ? trim($matches[1]) : $naksatra1;
  $pada1 = $matches[2] ?? null;
  preg_match('/([a-zA-z ]*),?.*(\d)/', $naksatra2, $matches);
  $naksatra2 = isset($matches[1]) ? trim($matches[1]) : $naksatra2;
  $pada2 = $matches[2] ?? null;

  if (
    isset($naksatraNames->{$naksatra1})
    && $naksatraNames->{$naksatra1} == $naksatra2
    && $pada1 == $pada2
  ) {
    return true;
  }

  if (
    isset($naksatraNames->{$naksatra2})
    && $naksatraNames->{$naksatra2} == $naksatra1
    && $pada1 == $pada2
  ) {
    return true;
  }

  return false;
}

if ($isAuthenticated) {
  if (isset($_GET['moonData'])) {
    require './moonData.php';
  }

  if (isset($_GET['names'])) {
    $stmt = $pdo->prepare("SELECT id, name, sex, birth_date, birth_time, birth_place, naksatra, pada, moon, info
      FROM devs
      WHERE inactive = '' OR inactive IS NULL
      ORDER BY name");
    $stmt->execute();
    $names = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($names);
    return;
  }

  if (isset($_GET['analysis'])) {
    $zodiacs = ['Aires', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 'Sagittarius', 'Capricorn', 'Aquarius', 'Pisces'];

    $chart = file_get_contents('../data/chart.json');
    $chart = json_decode($chart);

    $naksatraNames = file_get_contents('../data/naksatraNames.json');
    $naksatraNames = json_decode($naksatraNames);

    $stmt = $pdo->prepare("SELECT id, name, sex, YEAR(birth_date) AS birth_year, naksatra, pada, moon
      FROM devs
      WHERE id = ?");
    $stmt->execute([$_GET['analysis']]);
    $person = $stmt->fetch(PDO::FETCH_ASSOC);

    // select everybody for the other sex
    $selectedPersonSex = in_array($person['sex'], ['no', 'nő', 'female']) ? 'girl' : 'boy';

    $oppositeSex = ($selectedPersonSex == 'girl') ? '("férfi", "male")' : '("no", "nő", "female")';
    $stmt = $pdo->prepare("SELECT id, name, sex, birth_date, birth_time, birth_place, (" . $person['birth_year'] . " - YEAR(birth_date)) AS age_difference, naksatra, pada, moon
      FROM devs
      WHERE sex IN $oppositeSex
      AND (inactive = 0 OR inactive IS NULL)");
    $stmt->execute();
    $possiblePartners = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($possiblePartners as $i => $possiblePartner) {
      $possiblePartners[$i]['points'] = -1;

      if ($possiblePartner['naksatra'] && $possiblePartner['moon']) {
        // find the chart entry for this combination

        foreach ($chart as $chartData) {
          // points
          if (
            $selectedPersonSex == 'girl'
            && isSameNaksatras($chartData->girl, $person['naksatra'] . ', ' . $person['pada'], $naksatraNames)
            && isSameNaksatras($chartData->boy, $possiblePartner['naksatra'] . ', ' . $possiblePartner['pada'], $naksatraNames)
          ) {
            $possiblePartners[$i]['points'] = (float)$chartData->point;
          }
          if (
            $selectedPersonSex == 'boy'
            && isSameNaksatras($chartData->girl, $possiblePartner['naksatra'] . ', ' . $possiblePartner['pada'], $naksatraNames)
            && isSameNaksatras($chartData->boy, $person['naksatra'] . ', ' . $person['pada'], $naksatraNames)
          ) {
            $possiblePartners[$i]['points'] = (float)$chartData->point;
          }
        }

        $personMoonPosition = array_search($person['moon'], $zodiacs);
        $possiblePartnerMoonPosition = array_search($possiblePartner['moon'], $zodiacs);

        // female - male
        if ($selectedPersonSex == 'girl') {
          $moonPositionDifference = $personMoonPosition - $possiblePartnerMoonPosition;
        }
        if ($selectedPersonSex == 'boy') {
          $moonPositionDifference =  $possiblePartnerMoonPosition - $personMoonPosition;
        }

        // rashi
        // male - female
        $possiblePartners[$i]['rashi'] = -$moonPositionDifference;

        // TODO now we calculate on client side, get it back to server side #9
        $possiblePartners[$i]['stridirgha'] = 'TODO #9';
      }
    }

    echo json_encode($possiblePartners);
    return;
  }
}

return;
