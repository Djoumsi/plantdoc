<?php
/**
 * =====================================================
 * PlantDoc — Front Controller
 * =====================================================
 */
declare(strict_types=1);

define('APP_ROOT', __DIR__);

// 1. Helpers d'abord (config dépend de h() etc.)
require APP_ROOT . '/config/env.php';
Env::load(APP_ROOT . '/.env');

// 2. Charger configs
$APP_CONFIG = require APP_ROOT . '/config/app.php';

// 3. Helpers fonctions
require APP_ROOT . '/helpers/html.php';
require APP_ROOT . '/helpers/i18n.php';
require APP_ROOT . '/helpers/csrf.php';
require APP_ROOT . '/helpers/logger.php';
require APP_ROOT . '/helpers/upload.php';
require APP_ROOT . '/helpers/ratelimit.php';

// 4. Autoload Composer (PHPMailer, etc.) si disponible
if (file_exists(APP_ROOT . '/vendor/autoload.php')) {
    require APP_ROOT . '/vendor/autoload.php';
}

// 4-bis. Autoload des classes (core, controllers, models, services)
spl_autoload_register(function (string $class): void {
    $dirs = ['core', 'controllers', 'models', 'services'];
    foreach ($dirs as $dir) {
        $file = APP_ROOT . "/$dir/$class.php";
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

// 5. Configuration
date_default_timezone_set(config('timezone'));
error_reporting(E_ALL);
ini_set('display_errors', config('debug') ? '1' : '0');

// 6. Session sécurisée
session_name(config('session')['name']);
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// 7. Définition des routes
$router = new Router();

// Public
$router->get('/',              'HomeController@index');
$router->get('/about',         'HomeController@about');
$router->get('/setlang/{code}','HomeController@setLanguage');
$router->get('/contact',       'HomeController@contactForm');
$router->post('/contact',      'HomeController@contactSend');

// Auth
$router->get('/login',         'AuthController@loginForm');
$router->post('/login',        'AuthController@login');
$router->get('/register',      'AuthController@registerForm');
$router->post('/register',     'AuthController@register');
$router->get('/logout',        'AuthController@logout');

// Diagnostic
$router->get('/dashboard',     'DiagnosticController@dashboard');
$router->get('/diagnostic/new','DiagnosticController@newForm');
$router->post('/diagnostic',   'DiagnosticController@create');
$router->get('/diagnostic/{id}','DiagnosticController@show');
$router->get('/diagnostic/{id}/pdf','DiagnosticController@pdf');
$router->post('/diagnostic/{id}/feedback','DiagnosticController@feedback');
$router->get('/history',       'DiagnosticController@history');

// Map
$router->get('/map',           'MapController@index');

// Profile
$router->get('/profile',          'ProfileController@show');
$router->post('/profile/update',  'ProfileController@update');
$router->post('/profile/password','ProfileController@password');

// Maladies (catalogue public)
$router->get('/maladies',      'MaladieController@index');
$router->get('/maladies/{id}', 'MaladieController@show');

// Admin
$router->get('/admin',                    'AdminController@dashboard');
$router->get('/admin/users',              'AdminController@users');
$router->get('/admin/diagnostics',        'AdminController@diagnostics');
$router->get('/admin/export/csv',         'AdminController@exportCsv');
$router->post('/admin/diagnostic/{id}/validate', 'AdminController@validate');
$router->get('/admin/maladies',           'AdminController@maladies');

// 8. Dispatch
try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $e) {
    Logger::error('Unhandled exception', ['msg' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
    if (config('debug')) {
        echo "<h1>Erreur</h1><pre>" . h($e->getMessage()) . "\n" . h($e->getTraceAsString()) . "</pre>";
    } else {
        http_response_code(500);
        echo "<h1>Erreur serveur</h1>";
    }
}
