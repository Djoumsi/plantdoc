<?php
/**
 * Chargement du fichier .env
 */
class Env
{
    private static array $vars = [];

    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new RuntimeException(".env introuvable : $path");
        }
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
            $v = trim($v, "\"' \t");
            self::$vars[trim($k)] = $v;
        }
    }

    public static function get(string $key, $default = null)
    {
        return self::$vars[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }
}
