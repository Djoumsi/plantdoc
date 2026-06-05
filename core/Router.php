<?php
/**
 * Routeur simple
 */
class Router
{
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?: '/';
        // Retirer le préfixe (sous-dossier WAMP), insensible à la casse
        $base = parse_url(config('url'), PHP_URL_PATH) ?: '';
        if ($base && stripos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
        }
        $uri = '/' . trim($uri, '/');

        $routes = $this->routes[$method] ?? [];

        // Recherche directe
        if (isset($routes[$uri])) {
            $this->call($routes[$uri], []);
            return;
        }

        // Recherche avec paramètres dynamiques {id}
        foreach ($routes as $pattern => $handler) {
            $regex = '#^' . preg_replace('/\{([a-z]+)\}/', '(?P<$1>[^/]+)', $pattern) . '$#';
            if (preg_match($regex, $uri, $m)) {
                $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->call($handler, $params);
                return;
            }
        }

        http_response_code(404);
        echo "<h1>404 — Page introuvable</h1><p>$uri</p>";
    }

    private function call($handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }
        [$ctrl, $action] = explode('@', $handler);
        $instance = new $ctrl();
        call_user_func_array([$instance, $action], $params);
    }
}
