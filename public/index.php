<?php

use App\App;

// on gére les backslah et les slash
const DS = DIRECTORY_SEPARATOR;

define('PATH_ROOT', dirname(__DIR__). DS);

require PATH_ROOT. 'vendor/autoload.php';

App::getApp()->start();