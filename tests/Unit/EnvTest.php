<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Core\Env;

class EnvTest extends TestCase
{
    /**
     * Test Env parser loads and converts values correctly.
     */
    public function testLoadAndGet(): void
    {
        $tempEnv = __DIR__ . '/.env.temp';
        file_put_contents($tempEnv, "TEST_VAR_ONE=hello\nTEST_VAR_TWO=true\nTEST_VAR_THREE=null\n");

        Env::load($tempEnv);
        @unlink($tempEnv);

        $this->assertEquals('hello', Env::get('TEST_VAR_ONE'));
        $this->assertTrue(Env::get('TEST_VAR_TWO'));
        $this->assertNull(Env::get('TEST_VAR_THREE'));
    }
}
