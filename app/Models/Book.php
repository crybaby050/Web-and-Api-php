<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Book
 *
 * Représente un livre du catalogue. Simple objet de données (pas de
 * logique métier ici) — la logique d'accès aux données vit dans
 * BookRepository, conformément à la séparation Modèle / Repository.
 */
final class Book
{
    public function __construct(
        public readonly int $id,
        public readonly string $titre,
        public readonly string $auteur,
        public readonly bool $disponible,
    ) {
    }

    /**
     * Construit un Book à partir d'une ligne PostgreSQL (tableau associatif).
     * PDO renvoie les booléens Postgres sous forme de chaînes ('t' / 'f')
     * ou déjà en bool selon le driver — on normalise ici pour être sûr.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            titre: (string) $row['titre'],
            auteur: (string) $row['auteur'],
            disponible: self::toBool($row['disponible']),
        );
    }

    /**
     * Sérialise le livre en tableau, prêt pour Response::json().
     *
     * @return array{id:int, titre:string, auteur:string, disponible:bool}
     */
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'titre'      => $this->titre,
            'auteur'     => $this->auteur,
            'disponible' => $this->disponible,
        ];
    }

    /**
     * Normalise une valeur booléenne issue de Postgres ('t'/'f', 0/1, bool).
     */
    private static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array($value, ['t', 'true', '1', 1], true);
    }
}