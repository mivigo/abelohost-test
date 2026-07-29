<?php

namespace App\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewarePipeline implements RequestHandlerInterface
{
    private int $index = 0;

    public function __construct(
        private array $middlewares,
        private RequestHandlerInterface $coreHandler
    ) {}

    /**
     * Handle the request by traversing the middleware pipeline.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->index < count($this->middlewares)) {
            $middleware = $this->middlewares[$this->index];
            $this->index++;
            
            $next = clone $this;
            return $middleware->process($request, $next);
        }

        return $this->coreHandler->handle($request);
    }
}
