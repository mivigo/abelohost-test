<?php

namespace App\Core\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class LoggerMiddleware implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger) {}

    /**
     * Process request and log metadata.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown';

        $this->logger->info("Request: {$method} {$path} from IP {$ip}");

        $startTime = microtime(true);
        $response = $handler->handle($request);
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        $status = $response->getStatusCode();
        $this->logger->info("Response: {$status} in {$duration}ms");

        return $response;
    }
}
