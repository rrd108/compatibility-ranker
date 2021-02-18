<?php
namespace CompatibilityRanker;

use Branca\Branca;
use PDO;

$isAuthenticated = false;

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: GET, PATCH, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  return;
}

require('./secrets.php');

if (isset($_GET['get-token'])) {
  require('./get-token.php');
  return;
}

if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
  header('HTTP/1.0 401 Unauthorized');
  return;
}

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
  $token = str_replace('ApiKey ', '', $_SERVER['HTTP_AUTHORIZATION']);

  $branca = new Branca($secrets['salt']);
  $userEmail = $branca->decode($token);

  $isAuthenticated = in_array($userEmail, array_keys($secrets['users']));
  if (!$isAuthenticated) {
    header('HTTP/1.0 401 Unauthorized');
    return;
  }
}

// user is authenticated

$pdo = new PDO('mysql:host=' . $secrets['mysql']['host'] . ';charset=utf8;dbname=' . $secrets['mysql']['database'], $secrets['mysql']['user'], $secrets['mysql']['pass']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  require './post.php';
}

if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
  require './patch.php';
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  require './get.php';
}
