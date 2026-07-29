<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Core\Container;
use App\Core\Exception\NotFoundException;

class ContainerTest extends TestCase
{
    /**
     * Test simple setting and getting from Container.
     */
    public function testSetAndGet(): void
    {
        $container = new Container();
        $container->set('foo', 'bar');

        $this->assertEquals('bar', $container->get('foo'));
    }

    /**
     * Test getting a non-existent item throws NotFoundException.
     */
    public function testGetNotFoundThrowsException(): void
    {
        $container = new Container();

        $this->expectException(NotFoundException::class);
        $container->get('non-existent');
    }

    /**
     * Test callable resolution matches singleton behavior.
     */
    public function testSingletonBehavior(): void
    {
        $container = new Container();
        
        $container->set('object', function () {
            return new \stdClass();
        });

        $obj1 = $container->get('object');
        $obj2 = $container->get('object');

        $this->assertSame($obj1, $obj2);
    }
}
