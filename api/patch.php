<?php

if ($isAuthenticated) {
  $data = json_decode(file_get_contents('php://input'));
  $data = (array) $data;

  if ($data['info']) {
    $stmt = $pdo->prepare("UPDATE devs SET info = :info WHERE id = :id");
  }

  if ($data['naksatra']) {
    $stmt = $pdo->prepare("UPDATE devs SET naksatra = :naksatra, pada=:pada, moon = :moon WHERE id = :id");
  }

  $result = $stmt->execute($data);
  echo json_encode($result);
}

return;
