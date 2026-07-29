<?php

namespace App\Core;

use Psr\Container\ContainerInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

abstract class Controller
{
    public function __construct(protected ContainerInterface $container) {}

    /**
     * Render a Smarty template.
     */
    protected function render(string $template, array $data = []): ResponseInterface
    {
        /** @var \Smarty $smarty */
        $smarty = $this->container->get(\Smarty::class);
        
        foreach ($data as $key => $value) {
            $smarty->assign($key, $value);
        }
        
        $body = $smarty->fetch($template);
        return $this->html($body);
    }

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
