<?php

declare(strict_types=1);

use App\Controllers\PageController;
use Core\Router;

/**
 * routes/web.php
 *
 * Routes WEB : destinées à l'humain, rendent du HTML via PageController.
 * Reçoit le Router déjà instancié (avec son Container) depuis le
 * front-controller public/index.php.
 *
 * @var Router $router
 */

// Page d'accueil
$router->get('/', [PageController::class, 'home']);

// Page catalogue (coquille HTML — la liste est chargée en JS via l'API)
$router->get('/books', [PageController::class, 'books']);