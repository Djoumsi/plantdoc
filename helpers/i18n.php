<?php
/**
 * Internationalisation (i18n) légère.
 *
 * Priorité de détection de la langue :
 *   1. session['lang'] (choix explicite via le sélecteur)
 *   2. cookie 'plantdoc_lang'
 *   3. langue par défaut 'fr'
 *
 * Les paquets de traduction sont dans /lang/{code}.php (tableaux clé => valeur).
 */

function available_langs(): array
{
    return [
        'fr' => ['label' => 'Français', 'flag' => '🇫🇷'],
        'en' => ['label' => 'English',  'flag' => '🇬🇧'],
    ];
}

function current_lang(): string
{
    $supported = array_keys(available_langs());
    $lang = $_SESSION['lang']
        ?? ($_COOKIE['plantdoc_lang'] ?? null)
        ?? 'fr';
    return in_array($lang, $supported, true) ? $lang : 'fr';
}

function set_lang(string $code): void
{
    $supported = array_keys(available_langs());
    if (!in_array($code, $supported, true)) {
        $code = 'fr';
    }
    $_SESSION['lang'] = $code;
    setcookie('plantdoc_lang', $code, time() + 31536000, '/');
}

/**
 * Charge (et met en cache) le paquet de traduction de la langue courante.
 */
function lang_pack(): array
{
    static $cache = [];
    $lang = current_lang();
    if (isset($cache[$lang])) {
        return $cache[$lang];
    }
    $file = APP_ROOT . "/lang/$lang.php";
    $pack = file_exists($file) ? require $file : [];
    // Repli sur le français pour les clés manquantes
    if ($lang !== 'fr') {
        $frFile = APP_ROOT . '/lang/fr.php';
        $fr = file_exists($frFile) ? require $frFile : [];
        $pack = array_merge($fr, $pack);
    }
    return $cache[$lang] = $pack;
}

/**
 * Traduit une clé. Retourne la clé si absente (facilite le repérage).
 * Supporte un remplacement simple : t('hello', ['name' => 'X']) avec {name}.
 */
function t(string $key, array $replace = []): string
{
    $pack = lang_pack();
    $text = $pack[$key] ?? $key;
    foreach ($replace as $k => $v) {
        $text = str_replace('{' . $k . '}', (string) $v, $text);
    }
    return $text;
}
