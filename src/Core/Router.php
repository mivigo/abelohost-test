<?php

namespace App\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use Nyholm\Psr7\Response;

class Router implements RequestHandlerInterface
{
    private array $routes = [];
    private array $globalMiddlewares = [];

    public function __construct(private ContainerInterface $container) {}

    /**
     * Map a route.
     */
    public function addRoute(string $method, string $path, string $controllerClass, string $action): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'controller' => $controllerClass,
            'action' => $action
        ];
    }

    /**
     * Add global middleware.
     */
    public function addMiddleware(\Psr\Http\Server\MiddlewareInterface $middleware): void
    {
        $this->globalMiddlewares[] = $middleware;
    }

    /**
     * Resolve request to route and process middleware pipeline.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestMethod = $request->getMethod();
        $requestPath = rawurldecode($request->getUri()->getPath());

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Replace parameters, e.g. {id} with regular expression
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $requestPath, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                $dispatcher = new RouteDispatcher(
                    $route['controller'],
                    $route['action'],
                    $params,
                    $this->container
                );

                $pipeline = new MiddlewarePipeline($this->globalMiddlewares, $dispatcher);
                return $pipeline->handle($request);
            }
        }

        // Return a clean 404 response
        return new Response(404, ['Content-Type' => 'text/html; charset=utf-8'], '<h1>404 Not Found</h1>');
    }
}
