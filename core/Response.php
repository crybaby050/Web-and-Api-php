<?php

declare(strict_types=1);

namespace Core;

/**
 * Response
 *
 * Représente une réponse HTTP destinée aux routes API : encapsule
 * le corps JSON et le code de statut HTTP à renvoyer. Le Router
 * détecte ce type de retour et se charge de l'envoyer correctement
 * (header Content-Type + code HTTP + corps JSON encodé).
 */
final class Response
{
    /**
     * @param array<mixed> $body       Contenu à encoder en JSON
     * @param int          $statusCode Code de statut HTTP (200, 201, 404, ...)
     */
    private function __construct(
        private readonly array $body,
        private readonly int $statusCode = 200,
    ) {
    }

    /**
     * Construit une réponse de succès : { "data": ... }
     */
    public static function json(mixed $data, int $statusCode = 200): self
    {
        return new self(['data' => $data], $statusCode);
    }

    /**
     * Construit une réponse d'erreur : { "error": "...", "code": ... }
     */
    public static function error(string $message, int $statusCode = 400): self
    {
        return new self(['error' => $message, 'code' => $statusCode], $statusCode);
    }

    /**
     * Envoie la réponse au client : header JSON, code HTTP, corps encodé.
     * Appelée par le Router une fois la route API exécutée.
     */
    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}