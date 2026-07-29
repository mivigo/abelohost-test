<?php

namespace App\Controllers;

use App\Core\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestController extends Controller
{
    /**
     * Test index action.
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        return $this->html("<h1>Hello from TestController index!</h1>");
    }

    /**
     * Test show action with attribute parameter.
     */
    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        return $this->html("<h1>Showing item with ID: {$id}</h1>");
    }
}
