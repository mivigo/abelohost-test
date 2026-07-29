<?php

namespace App\Core;

class Env
{
    /**
     * Load environment variables from a .env file into $_ENV and $_SERVER.
     */
    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip comments
            if (str_starts_with($line, '#')) {
                continue;
            }

            // Parse key-value pair
            if (!str_contains($line, '=')) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Strip quotes if they surround the value
            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            // Convert standard boolean and null string values
            if (strtolower($value) === 'true') {
                $value = true;
            } elseif (strtolower($value) === 'false') {
                $value = false;
            } elseif (strtolower($value) === 'null') {
                $value = null;
            }

            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv("$key=" . (is_bool($value) ? ($value ? 'true' : 'false') : (string)$value));
        }
    }

    /**
     * Get the value of an environment variable.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        $value = getenv($key);
        if ($value !== false) {
            if ($value === 'true') return true;
            if ($value === 'false') return false;
            if ($value === 'null') return null;
            return $value;
        }

        return $default;
    }
}
