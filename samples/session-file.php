<?php

require("../vendor/autoload.php");

use Rakit\Session\FileSessionHandler;

$path = __DIR__.'/session-files';

$sessionHandler = new FileSessionHandler($path, 'prefix_', '_postfix');

require("usage.php");