<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Env;

Env::load(__DIR__ . '/.env');

echo "<h1>PHP is working!</h1>";
echo "<p>App Name: " . Env::get('APP_NAME') . "</p>";
echo "<p>App Debug: " . (Env::get('APP_DEBUG') ? 'Yes' : 'No') . "</p>";

try {
    $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
        Env::get('DB_HOST'),
        Env::get('DB_PORT'),
        Env::get('DB_DATABASE')
    );
    $pdo = new PDO($dsn, Env::get('DB_USERNAME'), Env::get('DB_PASSWORD'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "<p style='color: green;'>Database connection successful!</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database connection failed: " . $e->getMessage() . "</p>";
}
