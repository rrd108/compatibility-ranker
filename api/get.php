<?php

namespace CompatibilityRanker;

use PDO;

if ($isAuthenticated) {
  if (isset($_GET['moonData'])) {
    require './moonData.php';
  }

  if (isset($_GET['names'])) {
    $stmt = $pdo->prepare("SELECT id, name, sex, birth_date, birth_time, birth_place, naksatra, moon, info
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

    $stmt = $pdo->prepare("SELECT id, name, sex, YEAR(birth_date) AS birth_year, naksatra, moon
      FROM devs
      WHERE id = ?");
    $stmt->execute([$_GET['analysis']]);
    $person = $stmt->fetch(PDO::FETCH_ASSOC);

    // select everybody for the other sex
    $sex = in_array($person['sex'], ['nő', 'female']) ? 'girl' : 'boy';

    $oppositeSex = ($sex == 'girl') ? '("férfi", "male")' : '("nő", "female")';
    $stmt = $pdo->prepare("SELECT id, name, birth_date, birth_time, birth_place, (" . $person['birth_year'] . " - YEAR(birth_date)) AS age_difference, naksatra, moon
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
          // female - male
          if ($sex == 'girl') {
            $moonPositionDifference = $personMoonPosition - $possiblePartnerMoonPosition;
          }
          if ($sex == 'boy') {
            $moonPositionDifference =  $possiblePartnerMoonPosition - $personMoonPosition;
          }
          // male - female
          $possiblePartners[$i]['rashi'] = -$moonPositionDifference;
          $possiblePartners[$i]['stridirgha'] = $moonPositionDifference > 0 ? $moonPositionDifference : 12 + $moonPositionDifference;
        }
      }
    }

    echo json_encode($possiblePartners);
    return;
  }
}

return;
