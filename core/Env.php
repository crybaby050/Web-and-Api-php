<?php

declare(strict_types=1);

namespace Core;

use RuntimeException;

/**
 * Env
 *
 * Charge les variables d'environnement définies dans un fichier .env
 * à la racine du projet, et les rend disponibles via $_ENV / getenv().
 *
 * Implémentation volontairement simple (sans dépendance externe) :
 * suffisante pour un projet pédagogique. Pour un vrai projet en
 * production, on préférera une librairie comme vlucas/phpdotenv.
 */
final class Env
{
    /** Empêche le rechargement répété du fichier .env */
    private static bool $loaded = false;

    /**
     * Lit le fichier .env et injecte chaque paire clé=valeur
     * dans $_ENV et via putenv(), pour que Database::getConnection()
     * (et le reste de l'application) puisse les lire.
     *
     * @param string $path Chemin vers le fichier .env
     *
     * @throws RuntimeException si le fichier .env est introuvable
     */
    public static function load(string $path): void
    {
        if (self::$loaded) {
            return;
        }

        if (!is_file($path)) {
            throw new RuntimeException(
                "Fichier .env introuvable : {$path}. " .
                "Copiez .env.example vers .env et renseignez vos identifiants."
            );
        }

        // Lit le fichier ligne par ligne, ignore les lignes vides et commentaires.
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // Ignore les commentaires (# ...)
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Une ligne valide doit contenir un signe '='
            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);

            // Retire les guillemets englobants éventuels : "valeur" ou 'valeur'
            $value = trim($value, "\"'");

            // N'écrase pas une variable déjà définie par le système
            // (permet de surcharger via l'environnement réel si besoin).
            if (getenv($key) === false) {
                putenv("{$key}={$value}");
                $_ENV[$key]    = $value;
                $_SERVER[$key] = $value;
            }
        }

        self::$loaded = true;
    }
}