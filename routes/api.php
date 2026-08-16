<?php

declare(strict_types=1);

use App\Controllers\BookApiController;
use Core\Router;

/**
 * routes/api.php
 *
 * Routes API : destinées au programme (JS), renvoient exclusivement
 * du JSON via BookApiController (objets Response).
 *
 * @var Router $router
 */

// Liste des livres
$router->get('/api/books', [BookApiController::class, 'index']);

// Création d'un livre
$router->post('/api/books', [BookApiController::class, 'store']);

// Bascule disponible / indisponible
$router->post('/api/books/{id}/toggle', [BookApiController::class, 'toggle']);

// Suppression d'un livre
$router->delete('/api/books/{id}', [BookApiController::class, 'destroy']);