<?php

namespace App\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;

class RouteDispatcher implements RequestHandlerInterface
{
    public function __construct(
        private string $controllerClass,
        private string $action,
        private array $routeParams,
        private ContainerInterface $container
    ) {}

    /**
     * Dispatch request to controller.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Inject matched parameters as request attributes
        foreach ($this->routeParams as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        // Instantiate controller
        $controller = $this->container->has($this->controllerClass)
            ? $this->container->get($this->controllerClass)
            : new $this->controllerClass($this->container);

        if (!method_exists($controller, $this->action)) {
            throw new RuntimeException("Action '{$this->action}' not found in controller '{$this->controllerClass}'");
        }

        return $controller->{$this->action}($request);
    }
}
