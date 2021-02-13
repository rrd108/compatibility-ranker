<?php

if ($isAuthenticated) {
  $data = json_decode(file_get_contents('php://input'));
  // UPDATE `devs` SET `egyeb6scale` = '' WHERE `devs`.`id` = 81;
  $data = (array) $data;
  $stmt = $pdo->prepare("UPDATE devs SET info = :info WHERE id = :id");
  $result = $stmt->execute($data);
  echo json_encode($result);
}

return;