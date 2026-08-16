<?php

declare(strict_types=1);

namespace Core;

use RuntimeException;

/**
 * View
 *
 * Moteur de rendu minimal : charge un fichier de vue PHP, l'exécute
 * dans un scope isolé avec les données fournies, puis injecte le
 * résultat dans le layout commun (base.layout.php).
 */
final class View
{
    /** Dossier racine contenant les vues (.php) */
    private static string $viewsPath = __DIR__ . '/../resources/views';

    /**
     * Rend une vue avec ses données, injectée dans le layout de base.
     *
     * @param string              $view Nom de la vue, sans extension (ex: "books")
     * @param array<string,mixed> $data Données transmises à la vue (extraites en variables locales)
     *
     * @throws RuntimeException si le fichier de vue n'existe pas
     */
    public static function render(string $view, array $data = []): string
    {
        $viewFile = self::$viewsPath . '/' . $view . '.php';

        if (!is_file($viewFile)) {
            throw new RuntimeException("Vue introuvable : {$viewFile}");
        }

        // Rendu du contenu spécifique à la vue, capturé dans $content.
        $content = self::capture($viewFile, $data);

        // Injection de $content dans le layout commun.
        $layoutFile = self::$viewsPath . '/layouts/base.layout.php';
        if (!is_file($layoutFile)) {
            throw new RuntimeException("Layout introuvable : {$layoutFile}");
        }

        return self::capture($layoutFile, array_merge($data, ['content' => $content]));
    }

    /**
     * Exécute un fichier PHP dans un scope isolé (les clés de $data
     * deviennent des variables locales), et capture sa sortie via
     * output buffering plutôt que de l'echo directement.
     */
    private static function capture(string $file, array $data): string
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require $file;

        return (string) ob_get_clean();
    }
}