<?php (isset($sessionHandler)) or die("Cannot access this file");

use Rakit\Session\SessionManager;

if('native' === $sessionHandler) $sessionHandler = null;

$session = new SessionManager($sessionHandler);

var_dump(array(
    'sessions' => $session->all(),
    'flash' => $session->flash->all(),
));

$session->foo = "qweqwe";
$session->bar = "asdasd";

if(!$session->flash->has("foo")) {
    echo "<br/><br/>I just set flash 'foo', refresh to see!";
    $session->flash->foo = "i am flash";
}