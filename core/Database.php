<?php
/**
 * Connexion PDO singleton
 */
class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $cfg = require __DIR__ . '/../config/database.php';
            $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['name']};charset={$cfg['charset']}";
            try {
                self::$instance = new PDO($dsn, $cfg['user'], $cfg['pass'], $cfg['options']);
            } catch (PDOException $e) {
                Logger::error('DB connect failed', ['msg' => $e->getMessage()]);
                throw new RuntimeException('Erreur de connexion à la base de données');
            }
        }
        return self::$instance;
    }
}
