<?php

declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;

/**
 * Database
 *
 * Gère la connexion unique (singleton) à la base de données PostgreSQL
 * via PDO. Toute la couche Repository passe par cette classe pour
 * obtenir l'instance PDO — on évite ainsi d'ouvrir une nouvelle
 * connexion à chaque requête.
 */
final class Database
{
    /** @var PDO|null Instance unique de connexion PDO */
    private static ?PDO $instance = null;

    /**
     * Empêche l'instanciation directe (classe purement statique).
     */
    private function __construct()
    {
    }

    /**
     * Retourne l'instance PDO connectée à PostgreSQL.
     * Crée la connexion au premier appel, puis la réutilise.
     *
     * @throws PDOException si la connexion échoue
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            // Paramètres de connexion — à adapter selon l'environnement
            // (idéalement lus depuis des variables d'environnement).
            $host     = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $port     = $_ENV['DB_PORT'] ?? '5432';
            $dbname   = $_ENV['DB_NAME'] ?? 'atelier22';
            $user     = $_ENV['DB_USER'] ?? 'postgres';
            $password = $_ENV['DB_PASSWORD'] ?? '';

            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

            try {
                self::$instance = new PDO($dsn, $user, $password, [
                    // Les erreurs SQL lèvent des exceptions plutôt que
                    // de renvoyer silencieusement `false`.
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    // Les résultats sont retournés sous forme de tableaux
                    // associatifs (clé = nom de colonne).
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // Désactive l'émulation des requêtes préparées :
                    // on utilise les vraies requêtes préparées de Postgres.
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                // On relance une exception plus explicite pour le débogage,
                // sans jamais exposer les identifiants de connexion.
                throw new PDOException(
                    'Connexion à la base de données impossible : ' . $e->getMessage(),
                    (int) $e->getCode()
                );
            }
        }

        return self::$instance;
    }
}