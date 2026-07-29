<?php

namespace App\Core;

use App\Helpers\LoggerTrait;
use Psr\Log\LoggerInterface;
use Throwable;

class ErrorHandler
{
    use LoggerTrait;

    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }

    /**
     * Handle and log an uncaught exception.
     */
    public function handle(Throwable $exception): void
    {
        $this->logError("Uncaught exception: {message} in {file} on line {line}\nStack trace:\n{trace}", [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
