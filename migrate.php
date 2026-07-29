<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Env;
use App\Core\Database;

Env::load(__DIR__ . '/.env');

try {
    $db = Database::getConnection();
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Create migrations table if not exists
$db->exec("
    CREATE TABLE IF NOT EXISTS `migrations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `migration` VARCHAR(255) NOT NULL,
        `batch` INT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$action = $argv[1] ?? 'up';

try {
    match ($action) {
        'up' => runMigrations($db),
        'rollback', 'down' => rollbackMigrations($db),
        'status' => showStatus($db),
        default => throw new InvalidArgumentException("Unknown command: {$action}\nUsage: php migrate.php [up|rollback|status]\n")
    };
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
    exit(1);
}


function runMigrations(PDO $db)
{
    echo "Running migrations...\n";
    
    // Get applied migrations
    $stmt = $db->query("SELECT `migration` FROM `migrations`");
    $applied = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Get all migration files
    $files = glob(__DIR__ . '/migrations/*.php');
    sort($files);

    $pending = [];
    foreach ($files as $file) {
        $name = basename($file, '.php');
        if ($name === '.gitkeep') continue;
        if (!in_array($name, $applied)) {
            $pending[] = [
                'file' => $file,
                'name' => $name
            ];
        }
    }

    if (empty($pending)) {
        echo "Nothing to migrate.\n";
        return;
    }

    // Get current batch number
    $batchStmt = $db->query("SELECT MAX(`batch`) FROM `migrations`");
    $currentBatch = (int)$batchStmt->fetchColumn();
    $nextBatch = $currentBatch + 1;

    foreach ($pending as $p) {
        echo "Migrating: {$p['name']}... ";
        
        $className = "Database\\Migrations\\" . $p['name'];
        if (!class_exists($className)) {
            require_once $p['file'];
        }

        if (!class_exists($className)) {
            echo "Error: Class {$className} not found in {$p['file']}\n";
            exit(1);
        }

        /** @var \App\Core\Migration $migration */
        $migration = new $className();
        
        try {
            $migration->up();
            
            $insertStmt = $db->prepare("INSERT INTO `migrations` (`migration`, `batch`) VALUES (:migration, :batch)");
            $insertStmt->execute([
                'migration' => $p['name'],
                'batch' => $nextBatch
            ]);
            
            echo "OK\n";
        } catch (Exception $e) {
            echo "FAILED: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}

function rollbackMigrations(PDO $db)
{
    echo "Rolling back migrations...\n";
    
    // Get last batch
    $batchStmt = $db->query("SELECT MAX(`batch`) FROM `migrations`");
    $lastBatch = (int)$batchStmt->fetchColumn();

    if ($lastBatch === 0) {
        echo "Nothing to rollback.\n";
        return;
    }

    $stmt = $db->prepare("SELECT `migration` FROM `migrations` WHERE `batch` = :batch ORDER BY `id` DESC");
    $stmt->execute(['batch' => $lastBatch]);
    $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($migrations as $name) {
        echo "Rolling back: {$name}... ";
        
        $file = __DIR__ . "/migrations/{$name}.php";
        if (!file_exists($file)) {
            echo "Error: Migration file not found: {$file}\n";
            exit(1);
        }

        $className = "Database\\Migrations\\" . $name;
        if (!class_exists($className)) {
            require_once $file;
        }

        /** @var \App\Core\Migration $migration */
        $migration = new $className();
        
        try {
            $migration->down();
            
            $deleteStmt = $db->prepare("DELETE FROM `migrations` WHERE `migration` = :migration");
            $deleteStmt->execute(['migration' => $name]);
            
            echo "OK\n";
        } catch (Exception $e) {
            echo "FAILED: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}

function showStatus(PDO $db)
{
    $stmt = $db->query("SELECT * FROM `migrations` ORDER BY `id` ASC");
    $applied = $stmt->fetchAll();

    $files = glob(__DIR__ . '/migrations/*.php');
    sort($files);

    echo "Migration Status:\n";
    echo str_repeat('-', 70) . "\n";
    printf("%-40s | %-25s\n", "Migration Name", "Status");
    echo str_repeat('-', 70) . "\n";

    foreach ($files as $file) {
        $name = basename($file, '.php');
        if ($name === '.gitkeep') continue;
        
        $status = "Pending";
        foreach ($applied as $row) {
            if ($row['migration'] === $name) {
                $status = "Batch " . $row['batch'] . " (" . $row['created_at'] . ")";
                break;
            }
        }
        
        printf("%-40s | %-25s\n", $name, $status);
    }
    echo str_repeat('-', 70) . "\n";
}
