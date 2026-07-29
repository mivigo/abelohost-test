<?php
echo "<h1>PHP is working</h1>";
try {
    $dsn = "mysql:host=db;dbname=blog_db;charset=utf8mb4";
    $pdo = new PDO($dsn, 'blog_user', 'blog_password', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "<p style='color: green;'>DB successful</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>DB failed: " . $e->getMessage() . "</p>";
}
