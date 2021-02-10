<?php
use Branca\Branca;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = file_get_contents('php://input');

    if (isset(json_decode($data)->user) && isset(json_decode($data)->password)) {
        // with this identification passwords should be unique
        if(json_decode($data)->user == array_search(md5(json_decode($data)->password), $secrets['users'])) {

            $branca = new Branca($secrets['salt']);
            $payload = json_decode($data)->user;
            $token = $branca->encode($payload);

            echo $token;
            return;
        }
        echo json_encode(['error' => 'Invalid user or password']);
        return;
    }
}

header('HTTP/1.0 401 Unauthorized');
return;