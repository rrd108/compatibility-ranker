<?php

function getPlace($place)
{
  $client = new \GuzzleHttp\Client();
  $response = $client->request('GET', 'https://www.prokerala.com/astrology/search.php?index=0&q=' . substr($place, 0, 12));
  $place = json_decode($response->getBody());
  // TODO we can get multiple results and we have to manually select the correct one
  $place = explode('|', $place[0]);
  return $place;
}

function getNaksatra($person, $place)
{
  $client = new \GuzzleHttp\Client();
  $ampm = 'am';
  $hour = substr($person['birth_time'], 0, 2);
  if ($hour > 12) {
    $ampm = 'pm';
    $hour -= 12;
  }
  if ($hour == 0) {
    $ampm = 'am';
    $hour = 12;
  }

  $formData = [
    'name' => $person['name'],
    'gender' => in_array($person['sex'], ['no', 'nő', 'female']) ? 'female' : 'male',
    'year' => substr($person['birth_date'], 0, 4),
    'month' => substr($person['birth_date'], 5, 2),
    'day' => substr($person['birth_date'], 8, 2),
    'hour' => $hour,
    'min' => substr($person['birth_time'], 3, 2),
    'apm' => $ampm,
    'location' => $place[1] . ', ' . $place[2] . ', ' . $place[3],
    'loc' => $place[0],
    'location-change' => '1',
  ];

  $response = $client->request('POST', 'https://www.prokerala.com/astrology/nakshatra-finder/', [
    'form_params' => $formData
  ]);

  $response = $response->getBody()->getContents();

  preg_match('/<span class="t-large b">(.*)<sup>/', $response, $matches);
  /*$fp = fopen('./logs.log', 'a');
  fwrite($fp, $matches[1] . "\n");
  fclose($fp);*/
  return $matches[1];
}

function getMoon($person, $place)
{
  $client = new \GuzzleHttp\Client(['cookies' => true, 'version' => 2.0]);
  $response = $client->request('GET', 'https://cafeastrology.com/whats-my-moon-sign.html');

  $geonamesClient = new \GuzzleHttp\Client();
  $response = $geonamesClient->request('GET', 'https://secure.geonames.org/searchJSON?name_startsWith='
    . $place . '&featureClass=P&style=full&maxRows=20&username=cafeastrology&lang=');

  $place = json_decode($response->getBody());

  $formData = [
    'month' => substr($person['birth_date'], 5, 2),
    'day' => substr($person['birth_date'], 8, 2),
    'year' => substr($person['birth_date'], 0, 4),
    'hour' => substr($person['birth_time'], 0, 2),
    'minute' => substr($person['birth_time'], 3, 2),
    'zp-report-variation' => 'planet_lookup_moon',
    'place' => $place->geonames[0]->name . ', ' . $place->geonames[0]->adminName1 . ', ' . $place->geonames[0]->countryName,
    'geo_timezone_id' => $place->geonames[0]->timezone->timeZoneId,
    'zp_lat_decimal' => $place->geonames[0]->lat,
    'zp_long_decimal' => $place->geonames[0]->lng,
    'zp_offset_geo' => '',  // ? $place->geonames[0]->timezone->gmtOffset,
    'action' => 'zp_tz_offset',
  ];

  $response = $client->request(
    'POST',
    'https://cafeastrology.com/wp-admin/admin-ajax.php',
    [
      'form_params' => $formData,
      'headers' => [
        'Host' => 'cafeastrology.com',
        'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 ',
        'accept' => '*/*'
      ]
    ]
  );
  $response = json_decode($response->getBody());

  $formData['zp_offset_geo'] = $response->offset_geo;
  $formData['action'] = 'zp_birthreport';

  $response = $client->request(
    'POST',
    'https://cafeastrology.com/wp-admin/admin-ajax.php',
    [
      'form_params' => $formData,
      'headers' => [
        'Host' => 'cafeastrology.com',
        'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 ',
        'accept' => '*/*'
      ]
    ]
  );

  preg_match('/<p>You have Moon in ([^.]*)\./', $response->getBody(), $matches);
  return $matches[1];
}

if ($isAuthenticated) {
  if (isset($_GET['date']) && isset($_GET['time']) && isset($_GET['place'])) {
    $person = [
      'birth_date' => $_GET['date'],
      'birth_time' => $_GET['time'],
      'birth_place' => $_GET['place'],
    ];
    $naksatra = getNaksatra($person, getPlace($person['birth_place']));
    $moon = getMoon($person, $person['birth_place']);
  }

  if (is_numeric($_GET['moonData'])) {
    $stmt = $pdo->prepare("SELECT id, name, sex, birth_date, birth_time, birth_place, naksatra, moon
      FROM devs
      WHERE id = ?");
    $stmt->execute([$_GET['moonData']]);
    $person = $stmt->fetch(PDO::FETCH_ASSOC);
    $place = getPlace($person['birth_place']);
    $naksatra = getNaksatra($person, $place);
    $moon = getMoon($person, $person['birth_place']);
  }

  echo json_encode(['naksatra' => $naksatra, 'moon' => $moon]);

  return;
}
