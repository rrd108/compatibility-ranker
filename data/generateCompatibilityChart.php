<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$naksatras = $chart = [];

$line = 0;
$matrixSize = 108;
$delimiter = ',';

// create a total matrix
if (($handle = fopen('nakshatras.csv', 'r')) !== FALSE) {
  while (($rowDdata = fgetcsv($handle, 1200, $delimiter)) !== FALSE) {

    if ($line <= $matrixSize) {
      // 0. row is the list of nakshatras with padas
      if ($line == 0) {
        foreach ($rowDdata as $data) {
          if ($data) {
            $naksatras[] = $data;
          }
        }
      }

      if ($line >= 1) {
        foreach ($rowDdata as $i => $data) {
          if ($i > 1 && $i <= $matrixSize) {
            $chart[] = [
              'girl' => $naksatras[$i],
              'boy' => $rowDdata[0],
              'point' => $data
            ];
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
