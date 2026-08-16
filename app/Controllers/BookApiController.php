<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\BookRepository;
use Core\Response;

/**
 * BookApiController
 *
 * Gère les routes API (voir routes/api.php) : reçoit/renvoie du JSON
 * exclusivement, via des objets Response. Aucune sortie HTML ici.
 */
final class BookApiController
{
    private BookRepository $books;

    public function __construct()
    {
        $this->books = new BookRepository();
    }

    /**
     * GET /api/books
     * Liste tous les livres du catalogue.
     */
    public function index(): Response
    {
        $books = array_map(
            fn ($book) => $book->toArray(),
            $this->books->all()
        );

        return Response::json($books);
    }

    /**
     * POST /api/books
     * Crée un nouveau livre à partir du corps JSON de la requête.
     * Corps attendu : { "titre": "...", "auteur": "..." }
     */
    public function store(): Response
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        $titre  = trim((string) ($input['titre'] ?? ''));
        $auteur = trim((string) ($input['auteur'] ?? ''));

        if ($titre === '' || $auteur === '') {
            return Response::error('Le titre et l\'auteur sont obligatoires.', 422);
        }

        $book = $this->books->create($titre, $auteur);

        return Response::json($book->toArray(), 201);
    }

    /**
     * POST /api/books/{id}/toggle
     * Inverse la disponibilité d'un livre.
     */
    public function toggle(string $id): Response
    {
        $book = $this->books->toggleDisponible((int) $id);

        if ($book === null) {
            return Response::error('Livre introuvable', 404);
        }

        return Response::json($book->toArray());
    }

    /**
     * DELETE /api/books/{id}
     * Supprime un livre.
     */
    public function destroy(string $id): Response
    {
        if (!$this->books->delete((int) $id)) {
            return Response::error('Livre introuvable', 404);
        }

        return Response::json(['supprime' => true]);
    }
}