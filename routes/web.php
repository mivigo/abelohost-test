<?php

use App\Controllers\HomeController;
use App\Controllers\CategoryController;
use App\Controllers\PostController;
use App\Core\Router;

/**
 * Register web routes on the application Router instance.
 */
return function (Router $router): void {
    $router->addRoute('GET', '/', HomeController::class, 'index');
    $router->addRoute('GET', '/category/{id}', CategoryController::class, 'show');
    $router->addRoute('GET', '/post/{id}', PostController::class, 'show');
};
