<?php

require("../vendor/autoload.php");

use Rakit\Session\PdoSessionHandler;

$pdo = new PDO("mysql:host=localhost;dbname=rakit_session;", "root", "");
$tablename = "sessions";

$sessionHandler = new PdoSessionHandler($pdo, $tablename);

require("usage.php");