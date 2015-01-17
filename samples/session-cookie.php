<?php

require("../vendor/autoload.php");

use Rakit\Session\CookieSessionHandler;

$sessionHandler = new CookieSessionHandler('sess_cookie');

require("usage.php");