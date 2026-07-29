<?php

namespace App\Core;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    public function __construct(private string $logFile)
    {
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    /**
     * Log a message with a specific level.
     */
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $date = date('Y-m-d H:i:s');
        $formattedMessage = $this->interpolate((string)$message, $context);
        $logEntry = sprintf("[%s] %s: %s\n", $date, strtoupper($level), $formattedMessage);
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }

    /**
     * Interpolates context values into the message placeholders.
     */
    private function interpolate(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        return strtr($message, $replace);
    }
}
