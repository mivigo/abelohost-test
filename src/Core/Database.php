<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $connection = null;

    /**
     * Get the PDO database connection singleton.
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
                    Env::get('DB_HOST', 'db'),
                    Env::get('DB_PORT', '3306'),
                    Env::get('DB_DATABASE', 'blog_db')
                );
                
                self::$connection = new PDO($dsn, 
                    Env::get('DB_USERNAME', 'blog_user'), 
                    Env::get('DB_PASSWORD', 'blog_password'), 
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                throw new RuntimeException("Database connection error: " . $e->getMessage(), 0, $e);
            }
        }

        return self::$connection;
    }
}
