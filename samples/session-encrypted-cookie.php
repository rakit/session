<?php

require("../vendor/autoload.php");

use Rakit\Session\CookieSessionHandler;

$sessionHandler = new CookieSessionHandler('sess_encrypted_cookie', array(
    'mcrypt_key' => '09182301820391803981029312',
));

require("usage.php");