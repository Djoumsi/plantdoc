<?php
/**
 * Échappement XSS
 */
function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * URL absolue
 */
function url(string $path = ''): string
{
    return rtrim(config('url'), '/') . '/' . ltrim($path, '/');
}

/**
 * Asset (CSS/JS/img)
 */
function asset(string $path): string
{
    return url('public/' . ltrim($path, '/'));
}

/**
 * Accès config rapide
 */
function config(string $key, $default = null)
{
    global $APP_CONFIG;
    return $APP_CONFIG[$key] ?? $default;
}

/**
 * Flash message
 */
function flash(string $key): ?string
{
    if (!empty($_SESSION["flash_$key"])) {
        $msg = $_SESSION["flash_$key"];
        unset($_SESSION["flash_$key"]);
        return $msg;
    }
    return null;
}

function set_flash(string $key, string $msg): void
{
    $_SESSION["flash_$key"] = $msg;
}

/**
 * Vérifier si utilisateur connecté
 */
function auth(): bool
{
    return !empty($_SESSION['user_id']);
}

function user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function user_role(): ?string
{
    return $_SESSION['user_role'] ?? null;
}

/**
 * Vérifier rôle
 */
function is_admin(): bool
{
    return user_role() === 'admin';
}

function is_expert(): bool
{
    return user_role() === 'expert';
}

/**
 * Formattage date
 */
function format_date(string $date, string $format = 'd/m/Y H:i'): string
{
    return date($format, strtotime($date));
}

function time_ago(string $date): string
{
    $diff = time() - strtotime($date);
    if ($diff < 60)    return "à l'instant";
    if ($diff < 3600)  return floor($diff / 60) . ' min';
    if ($diff < 86400) return floor($diff / 3600) . ' h';
    if ($diff < 604800) return floor($diff / 86400) . ' j';
    return format_date($date, 'd M Y');
}
