<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = self::loadConfig();

            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['name'],
                $config['charset']
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, 
            ];

            try {
                self::$instance = new PDO($dsn, $config['user'], $config['pass'], $options);
            } catch (PDOException $e) {
                
                error_log('[Database] Connection failed: ' . $e->getMessage());
                throw new RuntimeException('Veritabanı bağlantısı kurulamadı.');
            }
        }

        return self::$instance;
    }

    private static function loadConfig(): array
    {
        $configPath = dirname(__DIR__, 2) . '/config/config.php';
        $all = require $configPath;

        return $all['db'];
    }
}
