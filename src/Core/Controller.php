<?php

namespace App\Core;

use Psr\Container\ContainerInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

abstract class Controller
{
    public function __construct(protected ContainerInterface $container) {}

    /**
     * Create html response.
     */
    protected function html(string $body, int $status = 200): ResponseInterface
    {
        return new Response($status, ['Content-Type' => 'text/html; charset=utf-8'], $body);
    }

    /**
     * Create json response.
     */
    protected function json(mixed $data, int $status = 200): ResponseInterface
    {
        return new Response($status, ['Content-Type' => 'application/json'], json_encode($data));
    }

    /**
     * Create redirect response.
     */
    protected function redirect(string $url, int $status = 302): ResponseInterface
    {
        return new Response($status, ['Location' => $url]);
    }
}
