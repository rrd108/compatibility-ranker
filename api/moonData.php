<?php
function getPlace($place) {
  $client = new \GuzzleHttp\Client();
  $response = $client->request('GET', 'https://www.prokerala.com/astrology/search.php?index=0&q=' . $place);
  $place = json_decode($response->getBody());
  $place = explode('|', $place[0]);
  return $place;
}

function getNaksatra($person, $place) {
  $client = new \GuzzleHttp\Client();
  $ampm = 'am';
  $hour = substr($person['birth_time'], 0, 2);
  if ($hour > 12) {
    $ampm = 'pm';
    $hour -= 12;
  }

  $formData = [
    'year' => substr($person['birth_date'], 0, 4),
    'month' => (int) substr($person['birth_date'], 5, 2),
    'day' => (int) substr($person['birth_date'], 8, 2),
    'hour' => (int) $hour,
    'min' => (int) substr($person['birth_time'], 3, 2),
    'apm' => $ampm,
    'la' => 'en',
    'location' => $place[1] . ', ' . $place[2] . ', ' . $place[3],
    'loc' => $place[0],
    'location-change' => 1,
    'p' => 1
  ];

  $response = $client->request('POST', 'https://www.prokerala.com/astrology/nakshatra-finder/', [
    'form_params' => $formData
  ]);
  preg_match('/<span class="t-large b">(.*)<sup>/', $response->getBody(), $matches);
  return $matches[1];
}

function getMoon($person, $place) {
  // TODO we get forbidden, maybe because of the cookies
  $client = new \GuzzleHttp\Client();
  $jar = new \GuzzleHttp\Cookie\CookieJar();
  $response = $client->request('GET', 'https://cafeastrology.com/whats-my-moon-sign.html', ['cookies' => $jar]);

  $formData = [
    'month' => 3,
    'day' => 6,
    'year' => 1977,
    'hour' => 19,
    'minute' => 30,
    'zp-report-variation' => 'planet_lookup_moon',
    'place' => 'Kecskemét, Bács-Kiskun, Hungary',
    'geo_timezone_id' => 'Europe/Budapest',
    'zp_lat_decimal' => 46.90618,
    'zp_long_decimal' => 19.69128,
    'zp_offset_geo' => 1,
    'action' => 'zp_birthreport',
    'cookies' => $jar
  ];

  $response = $client->request('POST', 'https://cafeastrology.com/wp-admin/admin-ajax.php', $formData);
  preg_match('/<p>You have Moon in (.*)./', $response->getBody(), $matches);
  return $matches[1];
}

if ($isAuthenticated) {

  if ($_GET['date'] && $_GET['time'] && $_GET['place']) {
    $place = getPlace($person['birth_place']);
    $person = [
      'birth_date' => $_GET['date'],
      'birth_time' => $_GET['time']
    ];
    $naksatra = getNaksatra($person, $place);
  }

  if (is_numeric($_GET['moonData'])) {
    $stmt = $pdo->prepare("SELECT id, name, birth_date, birth_time, birth_place, naksatra, moon
      FROM devs
      WHERE id = ?");
    $stmt->execute([$_GET['moonData']]);
    $person = $stmt->fetch(PDO::FETCH_ASSOC);

    $place = getPlace($person['birth_place']);
    $naksatra = getNaksatra($person, $place);
    $moon = false;//getMoon($person, $place);
  }

  echo json_encode(['naksatra' => $naksatra, 'moon' => $moon]);

  return;
}