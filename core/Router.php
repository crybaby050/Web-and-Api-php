<?php

declare(strict_types=1);

namespace Core;

/**
 * Router
 *
 * Routeur minimal supportant les routes statiques et dynamiques
 * (paramètres {id}). Un même routeur gère aussi bien les routes
 * web (retour string → HTML) que les routes API (retour Response → JSON).
 */
final class Router
{
    /**
     * Routes enregistrées, groupées par méthode HTTP.
     * Structure : [ 'GET' => [ ['pattern' => '#^...$#', 'handler' => [...]], ... ], ... ]
     *
     * @var array<string, array<int, array{pattern: string, handler: array{0: string, 1: string}}>>
     */
    private array $routes = [];

    public function __construct(private readonly Container $container)
    {
    }

    public function get(string $uri, array $handler): void
    {
        $this->addRoute('GET', $uri, $handler);
    }

    public function post(string $uri, array $handler): void
    {
        $this->addRoute('POST', $uri, $handler);
    }

    public function delete(string $uri, array $handler): void
    {
        $this->addRoute('DELETE', $uri, $handler);
    }

    /**
     * Enregistre une route en transformant les segments {param} en
     * groupes capturants d'expression régulière.
     *
     * @param array{0: string, 1: string} $handler [Classe du contrôleur, méthode à appeler]
     */
    private function addRoute(string $method, string $uri, array $handler): void
    {
        // Transforme "/api/books/{id}" en "#^/api/books/([^/]+)$#"
        $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', rtrim($uri, '/') ?: '/');
        $pattern = '#^' . $pattern . '$#';

        $this->routes[$method][] = [
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    /**
     * Résout et exécute la route correspondant à la requête courante.
     * - Si le handler retourne une string  → sortie HTML directe (route web).
     * - Si le handler retourne une Response → envoi JSON via Response::send() (route API).
     */
    public function dispatch(string $method, string $uri): void
    {
        $uri = rtrim(parse_url($uri, PHP_URL_PATH) ?: '/', '/') ?: '/';

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                // On ne garde que les groupes capturés (les paramètres {id}, {slug}, ...)
                $params = array_slice($matches, 1);

                [$controllerClass, $action] = $route['handler'];
                $controller = $this->container->get($controllerClass);

                $result = $controller->{$action}(...$params);

                if ($result instanceof Response) {
                    $result->send();
                } else {
                    echo $result;
                }

                return;
            }
        }

        // Aucune route trouvée : on distingue web (404 HTML) et API (404 JSON)
        // selon le préfixe de l'URI.
        if (str_starts_with($uri, '/api/')) {
            Response::error('Route introuvable', 404)->send();
        } else {
            http_response_code(404);
            echo '404 — Page introuvable';
        }
    }
}