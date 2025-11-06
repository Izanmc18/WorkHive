<?php

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../Helpers/Autoloader.php';

use App\Controllers\Router;

$router = new Router();
$router->router();

//echo 'funciona';
?>