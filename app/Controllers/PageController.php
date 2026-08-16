<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\View;

/**
 * PageController
 *
 * Gère les routes WEB (voir routes/web.php) : sert des coquilles HTML
 * pour l'humain. Ne contient aucune logique de données — la liste des
 * livres est chargée côté client, en JS, via l'API (/api/books).
 */
final class PageController
{
    /**
     * Page d'accueil.
     */
    public function home(): string
    {
        return View::render('home', ['titre' => 'Accueil']);
    }

    /**
     * Page catalogue : coquille HTML + script books.js qui appelle l'API.
     */
    public function books(): string
    {
        return View::render('books', ['titre' => 'Catalogue']);
    }
}