<?php

use App\App;
use Dotenv\Dotenv;


// on gére les backslah et les slash
const DS = DIRECTORY_SEPARATOR;

define('PATH_ROOT', dirname(__DIR__). DS);

require PATH_ROOT. 'vendor/autoload.php';
//permet de charger le fichier .env
Dotenv::createImmutable(PATH_ROOT)->load();
//pour rcupérer les infos du .env, on utilise $env avec la clé qu'on veut récupérer
define('STRIPE_PK', $_ENV['STRIPE_PK']);
define('STRIPE_SK', $_ENV['STRIPE_SK']);



App::getApp()->start();