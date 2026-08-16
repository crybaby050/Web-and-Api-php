<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Book;
use Core\Database;
use PDO;

/**
 * BookRepository
 *
 * Seule classe autorisée à parler SQL pour la table `books`.
 * Toutes les requêtes utilisent des requêtes préparées PDO
 * (protection contre les injections SQL).
 *
 * Schéma attendu (PostgreSQL) :
 *
 *   CREATE TABLE books (
 *       id          SERIAL PRIMARY KEY,
 *       titre       VARCHAR(255) NOT NULL,
 *       auteur      VARCHAR(255) NOT NULL,
 *       disponible  BOOLEAN NOT NULL DEFAULT TRUE
 *   );
 */
final class BookRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Retourne tous les livres, triés par id.
     *
     * @return Book[]
     */
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id, titre, auteur, disponible FROM books ORDER BY id ASC');

        return array_map(
            fn (array $row) => Book::fromRow($row),
            $stmt->fetchAll()
        );
    }

    /**
     * Retourne un livre par son id, ou null s'il n'existe pas.
     */
    public function find(int $id): ?Book
    {
        $stmt = $this->pdo->prepare('SELECT id, titre, auteur, disponible FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        return $row === false ? null : Book::fromRow($row);
    }

    /**
     * Insère un nouveau livre (disponible = true par défaut) et le retourne.
     */
    public function create(string $titre, string $auteur): Book
    {
        // RETURNING permet de récupérer la ligne insérée en un seul aller-retour,
        // sans dépendre de lastInsertId() (spécificité pratique de Postgres).
        $stmt = $this->pdo->prepare(
            'INSERT INTO books (titre, auteur, disponible)
             VALUES (:titre, :auteur, TRUE)
             RETURNING id, titre, auteur, disponible'
        );
        $stmt->execute(['titre' => $titre, 'auteur' => $auteur]);

        return Book::fromRow($stmt->fetch());
    }

    /**
     * Inverse la disponibilité d'un livre (disponible <-> indisponible).
     * Retourne le livre mis à jour, ou null s'il n'existe pas.
     */
    public function toggleDisponible(int $id): ?Book
    {
        $stmt = $this->pdo->prepare(
            'UPDATE books
             SET disponible = NOT disponible
             WHERE id = :id
             RETURNING id, titre, auteur, disponible'
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        return $row === false ? null : Book::fromRow($row);
    }

    /**
     * Supprime un livre. Retourne true si une ligne a bien été supprimée.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}