<?php

if ($isAuthenticated) {
  $data = json_decode(file_get_contents('php://input'));
  $data->data->ranpass = substr(uniqid(), 0 ,6);
  $data->data->date = date('Y-m-d');
  $data = (array) $data->data;
  $stmt = $pdo->prepare("INSERT INTO devs (name, birth_date, birth_time, birth_place, sex, moon, naksatra, ranpass, date) VALUES (:name, :birth_date, :birth_time, :birth_place, :sex, :moon, :naksatra, :ranpass, :date)");
  $stmt->execute($data);
  $result = $pdo->lastInsertId();
  echo json_encode($result);
}

return;
