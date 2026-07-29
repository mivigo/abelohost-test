<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Core\Router;
use App\Core\Container;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Core\Controller;

class MockController extends Controller
{
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        return $this->html("mock_index");
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        return $this->html("mock_show_{$id}");
    }
}

class RouterTest extends TestCase
{
    /**
     * Test routing registration, dispatching, matching patterns and route parameters.
     */
    public function testRouteMatchingAndDispatch(): void
    {
        $container = new Container();
        $router = new Router($container);

        $router->addRoute('GET', '/items', MockController::class, 'index');
        $router->addRoute('GET', '/items/{id}', MockController::class, 'show');

        // Test matching simple index route
        $request1 = new ServerRequest('GET', '/items');
        $response1 = $router->handle($request1);
        $this->assertEquals(200, $response1->getStatusCode());
        $this->assertEquals('mock_index', (string)$response1->getBody());

        // Test matching dynamic show route and attribute parsing
        $request2 = new ServerRequest('GET', '/items/123');
        $response2 = $router->handle($request2);
        $this->assertEquals(200, $response2->getStatusCode());
        $this->assertEquals('mock_show_123', (string)$response2->getBody());

        // Test matching non-existent route returns 404
        $request3 = new ServerRequest('GET', '/non-existent');
        $response3 = $router->handle($request3);
        $this->assertEquals(404, $response3->getStatusCode());
    }
}
