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
  //var_dump($formData);

  $response = $client->request('POST', 'https://www.prokerala.com/astrology/nakshatra-finder/', [
    'form_params' => $formData
  ]);
  preg_match('/<span class="t-large b">(.*)<sup>/', $response->getBody(), $matches);
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

    // get moon
    // GET https://secure.geonames.org/searchJSON?name_startsWith=kecskem%C3%A9t&featureClass=P&style=full&maxRows=20&username=cafeastrology&lang=
    /* {"totalResultsCount":3,
      "geonames":[
        {"timezone":{
          "gmtOffset":1,
          "timeZoneId":"Europe/Budapest",
          "dstOffset":2
        },
        "bbox":{
          "east":19.754115627304806,
          "south":46.863286809608795,
          "north":46.949080750686406,
          "west":19.628437473281195,
          "accuracyLevel":2
        },
        "asciiName":"Kecskemet",
        "astergdem":122,
        "countryId":"719819",
        "fcl":"P",
        "srtm3":120,
        "score":27.75844955444336,
        "cc2":"HU",
        "countryCode":"HU","
        adminCodes1":{"ISO3166_2":"BK"},
        "adminId1":"3055744",

        "lat":"46.90618",

        "fcode":"PPLA",
        "continentCode":"EU",
        "adminCode1":"01",

        "lng":"19.69128",

        "geonameId":3050434,"toponymName":"Kecskemét","population":109847,"adminName5":"","adminName4":"","adminName3":"","alternateNames":[{"name":"Кечкемет","lang":"bg"},{"name":"Kecskemét","lang":"de"},{"name":"Kecskemét","lang":"en"},{"name":"Kecskemét","lang":"eo"},{"name":"کچکمیت","lang":"fa"},{"name":"Kecskemét","lang":"fi"},{"name":"Kecskemét","lang":"fr"},{"name":"קצ'קמט","lang":"he"},{"name":"Kečkemet","lang":"hr"},{"name":"Kecskemét","lang":"hu"},{"name":"Kecskemét","lang":"it"},{"name":"ケチケメート","lang":"ja"},{"name":"케치케메트","lang":"ko"},{"name":"Kečkemėtas","lang":"lt"},{"name":"Кечкемет","lang":"mk"},{"name":"Кечкемет","lang":"mrj"},{"name":"Kecskemét","lang":"nl"},{"name":"Kecskemét","lang":"pl"},{"name":"کچکیمت","lang":"pnb"},{"name":"Kecskemét","lang":"pt"},{"name":"Kecskemét","lang":"ro"},{"name":"Кечкемет","lang":"ru"},{"isPreferredName":true,"name":"Kečkemet","lang":"sr"},{"name":"Kecskemét","lang":"sv"},{"name":"เคชเคเมต","lang":"th"},{"name":"Кечкемет","lang":"uk"},{"name":"HUKCS","lang":"unlc"},{"name":"Q171357","lang":"wkdt"},{"name":"凯奇凯梅特","lang":"zh"},{"name":"凯奇凯梅特","lang":"zh-CN"}],"adminName2":"","name":"Kecskemét","fclName":"city, village,...","countryName":"Hungary","fcodeName":"seat of a first-order administrative division","adminName1":"Bács-Kiskun"},{"timezone":{"gmtOffset":1,"timeZoneId":"Europe/Budapest","dstOffset":2},"bbox":{"east":20.81349995368667,"south":48.20767305124824,"north":48.22566034875175,"west":20.786500046313332,"accuracyLevel":1},"asciiName":"Kecskemetitanya","astergdem":119,"countryId":"719819","fcl":"P","srtm3":120,"score":5.579575538635254,"countryCode":"HU","adminCodes1":{"ISO3166_2":"BZ"},"adminId1":"722064","lat":"48.21667","fcode":"PPL","continentCode":"EU","adminCode1":"04","lng":"20.8","geonameId":719298,"toponymName":"Kecskemétitanya","population":0,"adminName5":"","adminName4":"","adminName3":"","adminName2":"","name":"Kecskemétitanya","fclName":"city, village,...","countryName":"Hungary","fcodeName":"populated place","adminName1":"Borsod-Abaúj-Zemplén"},{"timezone":{"gmtOffset":1,"timeZoneId":"Europe/Budapest","dstOffset":2},"bbox":{"east":19.33041124437706,"south":47.24795854049943,"north":47.25515345950057,"west":19.31981075562294,"accuracyLevel":1},"asciiName":"Kecskemetitanya","astergdem":114,"countryId":"719819","fcl":"P","srtm3":112,"score":5.579575538635254,"countryCode":"HU","adminCodes1":{"ISO3166_2":"PE"},"adminId1":"3046431","lat":"47.25156","fcode":"PPLX","continentCode":"EU","adminCode1":"16","lng":"19.32511","geonameId":3050431,"toponymName":"Kecskemétitanya","population":0,"adminName5":"","adminName4":"","adminName3":"","adminName2":"","name":"Kecskemétitanya","fclName":"city, village,...","countryName":"Hungary","fcodeName":"section of populated place","adminName1":"Pest"}]}
    */

    // POST https://cafeastrology.com/wp-admin/admin-ajax.php
    /*
      month: 3
      day: 6
      year: 1977
      hour: 19
      minute: 30
      zp-report-variation: planet_lookup_moon
      place: Kecskemét, Bács-Kiskun, Hungary
      geo_timezone_id: Europe/Budapest
      zp_lat_decimal: 46.90618
      zp_long_decimal: 19.69128
      zp_offset_geo: 1
      action: zp_birthreport
    */
    // {"report":"<div class=\"zp-planet-lookup-output\">&nbsp;\r\n\r\n<h4>Planet Lookup Result:<\/h4>\r\n\r\n&nbsp;\r\n\r\n<div class=\"content-box-red\"><p>You have Moon in Virgo. <\/p>\r\n\r\n<p>The symbol, or glyph, for Virgo is <span class=\"zp-icon-virgo\"> <\/span>. <\/p>\r\n\r\n<p>Your Moon is at the following degree(s): 29&#176; <span class=\"zp-icon-virgo\"> <\/span> 57' 23\"<\/p><\/div> <br \/><a href=\"\">Look Up Another<\/a><\/div>","image":""}
  }

  echo json_encode(['naksatra' => $naksatra]);

  return;
}