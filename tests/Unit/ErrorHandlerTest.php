<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Core\Logger;
use App\Core\ErrorHandler;
use Exception;

class ErrorHandlerTest extends TestCase
{
    private string $tempAppLog;
    private string $errorLogDir;

    /**
     * Clean target file locations before starting checks.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->tempAppLog = __DIR__ . '/../../storage/logs/test_app.log';
        $this->errorLogDir = __DIR__ . '/../../storage/logs/error';
        
        if (file_exists($this->tempAppLog)) {
            unlink($this->tempAppLog);
        }
        
        $todayErrorLog = $this->errorLogDir . '/' . date('Y-m-d') . '_errors.log';
        if (file_exists($todayErrorLog)) {
            unlink($todayErrorLog);
        }
    }

    /**
     * Wipe created temporary log structures after finishing test checks.
     */
    protected function tearDown(): void
    {
        if (file_exists($this->tempAppLog)) {
            unlink($this->tempAppLog);
        }
        
        $todayErrorLog = $this->errorLogDir . '/' . date('Y-m-d') . '_errors.log';
        if (file_exists($todayErrorLog)) {
            unlink($todayErrorLog);
        }
        parent::tearDown();
    }

    /**
     * Verify level-based log routing to separate target destinations.
     */
    public function testLoggerRoutesLevelsToCorrectFiles(): void
    {
        $logger = new Logger($this->tempAppLog);

        // 1. Info level should reside in normal log destination
        $logger->info("This is an info message");
        $this->assertFileExists($this->tempAppLog);
        $this->assertStringContainsString("INFO: This is an info message", file_get_contents($this->tempAppLog));

        // 2. Error level should get routed to /storage/logs/error/{today}_errors.log
        $logger->error("This is a severe error message");
        
        $todayErrorLog = $this->errorLogDir . '/' . date('Y-m-d') . '_errors.log';
        $this->assertFileExists($todayErrorLog);
        $this->assertStringContainsString("ERROR: This is a severe error message", file_get_contents($todayErrorLog));
        
        // Ensure error log didn't pollute the info log file
        $this->assertStringNotContainsString("severe error", file_get_contents($this->tempAppLog));
    }

    /**
     * Verify central error handler formats exceptions and writes stack traces.
     */
    public function testErrorHandlerLogsExceptionWithStackTrace(): void
    {
        $logger = new Logger($this->tempAppLog);
        $handler = new ErrorHandler($logger);

        $exception = new Exception("Test exception for unit test code");
        $handler->handle($exception);

        $todayErrorLog = $this->errorLogDir . '/' . date('Y-m-d') . '_errors.log';
        $this->assertFileExists($todayErrorLog);

        $logContent = file_get_contents($todayErrorLog);
        $this->assertStringContainsString("ERROR: Uncaught exception: Test exception for unit test code", $logContent);
        $this->assertStringContainsString("Stack trace:", $logContent);
        $this->assertStringContainsString("ErrorHandlerTest.php", $logContent);
    }
}
