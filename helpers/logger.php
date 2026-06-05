<?php
class Logger
{
    public static function log(string $level, string $message, array $context = []): void
    {
        $file = APP_ROOT . '/logs/app.log';
        $line = sprintf(
            "[%s] %s: %s %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            $context ? json_encode($context, JSON_UNESCAPED_UNICODE) : ''
        );
        @file_put_contents($file, $line, FILE_APPEND);
    }

    public static function info(string $msg, array $ctx = []): void  { self::log('info', $msg, $ctx); }
    public static function warn(string $msg, array $ctx = []): void  { self::log('warn', $msg, $ctx); }
    public static function error(string $msg, array $ctx = []): void { self::log('error', $msg, $ctx); }
}
