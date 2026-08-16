<?php

declare(strict_types=1);

use Core\Container;
use Core\Env;
use Core\Router;

// Remplace vendor/autoload.php : autoloading maison, sans dépendance Composer.
require __DIR__ . '/../bootstrap/autoload.php';

Env::load(__DIR__ . '/../.env');

$container = new Container();
$router = new Router($container);

require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/api.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

$router->dispatch($method, $uri);