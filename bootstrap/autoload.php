<?php

declare(strict_types=1);

/**
 * bootstrap/autoload.php
 *
 * Autoloader PSR-4 minimal, écrit à la main (sans Composer).
 * Associe chaque namespace racine à son dossier physique, puis
 * reconstruit le chemin du fichier à charger à partir du nom
 * pleinement qualifié de la classe demandée.
 *
 * Namespaces gérés :
 *   Core\...  → core/...
 *   App\...   → app/...
 */

spl_autoload_register(function (string $class): void {
    // Table de correspondance namespace racine → dossier physique.
    $namespaces = [
        'Core\\' => __DIR__ . '/../core/',
        'App\\'  => __DIR__ . '/../app/',
    ];

    foreach ($namespaces as $prefix => $baseDir) {
        // La classe ne relève pas de ce namespace : on passe au suivant.
        if (!str_starts_with($class, $prefix)) {
            continue;
        }

        // Retire le préfixe (ex: "App\Controllers\PageController" → "Controllers\PageController")
        $relativeClass = substr($class, strlen($prefix));

        // Convertit les séparateurs de namespace en séparateurs de dossier,
        // et ajoute l'extension .php (convention PSR-4).
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require $file;
        }

        return;
    }
});