<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

function getNaksatraPadas($naksatraWithPadas) {
  // find the first number
  preg_match('/\d/', $naksatraWithPadas, $matches);
  $firstNumberPosition = strpos($naksatraWithPadas, $matches[0]);
  $naksatraName = substr($naksatraWithPadas, 0, $firstNumberPosition - 1);
  $padas = explode(',', substr($naksatraWithPadas, $firstNumberPosition));
  return [$naksatraName, $padas];
}

$naksatras = $chart = [];

$line = 0;

// create a total matrix
if (($handle = fopen('nakshatras.csv', 'r')) !== FALSE) {
  while (($rowDdata = fgetcsv($handle, 1000, ';')) !== FALSE) {

    // 0. row is the zodiacs, we do not need it
    if ($line < 38) {

      // 1. row is the list of nakshatras with padas
      if ($line == 1) {
        foreach ($rowDdata as $data) {
          if ($data) {
            $naksatras[] = $data;
          }
        }
      }

      if ($line >= 2) {
        foreach ($rowDdata as $i => $data) {
          if ($i == 1) {
            $boy = $data;
          }

          if ($i > 1 && $i < 38) {
            // 'girl' => string 'Ashvini 1,2,3,4' (length=15)
            // 'boy' => string 'Bharani 1,2,3,4' (length=15)

            $naksatraPada = $naksatras[$i - 2];

            list($naksatraNameGirl, $padasGirl) = getNaksatraPadas($naksatraPada);
            list($naksatraNameBoy, $padasBoy) = getNaksatraPadas($boy);

            foreach ($padasGirl as $padaGirl) {
              foreach ($padasBoy as $padaBoy) {
                $chart[] = [
                  'girl' => $naksatraNameGirl . ', ' . $padaGirl,
                  'boy' => $naksatraNameBoy . ', ' . $padaBoy,
                  'point' => $data
                ];
              }
            }

          }
        }
      }

      $line++;
    }
  }
  fclose($handle);
}

$fp = fopen('chart.json', 'w');
fwrite($fp, json_encode($chart));
fclose($fp);