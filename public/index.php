<?php

declare(strict_types=1);

/**
 * public/index.php
 *
 * Front-controller unique : point d'entrée de toute requête HTTP.
 * Responsabilités, dans l'ordre :
 *   1. Charger l'autoloader et les variables d'environnement (.env)
 *   2. Instancier le Container et le Router
 *   3. Enregistrer les routes (web.php + api.php)
 *   4. Dispatcher la requête courante
 *
 * Sous PHP built-in server, lancer depuis public/ :
 *   php -S localhost:8080 index.php
 *
 * Sous Apache (XAMPP), pointer le vhost vers ce dossier public/
 * avec un .htaccess qui redirige tout vers index.php (voir plus bas).
 */

use Core\Container;
use Core\Env;
use Core\Router;

require __DIR__ . '/../vendor/autoload.php';

// --- 1. Environnement ----------------------------------------------------

// Charge les identifiants de connexion PostgreSQL depuis .env
// (voir core/Env.php). Doit être fait avant tout accès à la base.
Env::load(__DIR__ . '/../.env');

// --- 2. Container + Router ------------------------------------------------

$container = new Container();
$router = new Router($container);

// --- 3. Enregistrement des routes -----------------------------------------

// Chaque fichier de routes reçoit $router déjà construit et y attache
// ses routes (voir routes/web.php et routes/api.php).
require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/api.php';

// --- 4. Dispatch de la requête courante ------------------------------------

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

$router->dispatch($method, $uri);