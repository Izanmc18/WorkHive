<?php


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Helpers/Autoloader.php';

use App\Controllers\Router;

$router = new Router();
$router->router();


?>