<?php
/**
 * Configuration globale de l'application
 */
return [
    'name'      => Env::get('APP_NAME', 'PlantDoc'),
    'env'       => Env::get('APP_ENV', 'production'),
    'debug'     => filter_var(Env::get('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
    'url'       => Env::get('APP_URL', 'http://localhost'),
    'timezone'  => Env::get('APP_TIMEZONE', 'Africa/Douala'),

    'paths' => [
        'root'    => dirname(__DIR__),
        'views'   => dirname(__DIR__) . '/views',
        'uploads' => dirname(__DIR__) . '/public/uploads',
        'logs'    => dirname(__DIR__) . '/logs',
    ],

    'session' => [
        'name'     => 'PLANTDOC_SID',
        'lifetime' => (int) Env::get('SESSION_LIFETIME', 120),
    ],

    'upload' => [
        'max_size' => (int) Env::get('UPLOAD_MAX_SIZE', 5_242_880),
        'allowed'  => explode(',', Env::get('UPLOAD_ALLOWED', 'image/jpeg,image/png,image/webp')),
    ],

    'ai' => [
        'api_key'   => Env::get('ANTHROPIC_API_KEY'),
        'model'     => Env::get('ANTHROPIC_MODEL', 'claude-haiku-4-5-20251001'),
        'max_tokens'=> (int) Env::get('ANTHROPIC_MAX_TOKENS', 1024),
    ],

    'mail' => [
        'from'      => Env::get('MAIL_FROM', 'no-reply@plantdoc.cm'),
        'from_name' => Env::get('MAIL_FROM_NAME', 'PlantDoc'),
        // Pilotes : 'smtp' (PHPMailer + Gmail/Outlook…), 'mail' (fonction mail() PHP), 'log' (journal)
        'driver'    => Env::get('MAIL_DRIVER', 'log'),
    ],

    'rate_limits' => [
        'login'      => (int) Env::get('RATE_LOGIN', 5),
        'diagnostic' => (int) Env::get('RATE_DIAGNOSTIC', 10),
        'register'   => (int) Env::get('RATE_REGISTER', 3),
    ],
];
