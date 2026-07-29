<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Env;
use App\Core\Container;
use App\Core\Logger;
use App\Core\Router;
use App\Core\Middleware\LoggerMiddleware;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

// Load environment variables
Env::load(__DIR__ . '/.env');

// Initialize DI Container (PSR-11)
$container = new Container();

// Bind core services
$container->set(Psr\Log\LoggerInterface::class, function () {
    return new Logger(Env::get('LOG_PATH', __DIR__ . '/storage/logs/app.log'));
});

$container->set(Smarty::class, function () {
    $smarty = new Smarty();
    $smarty->setTemplateDir(__DIR__ . '/view');
    $smarty->setCompileDir(__DIR__ . '/storage/templates_c');
    $smarty->setCacheDir(__DIR__ . '/storage/cache');
    return $smarty;
});

$container->set(Router::class, function ($c) {
    $router = new Router($c);
    
    // Register global logger middleware (PSR-15)
    $logger = $c->get(Psr\Log\LoggerInterface::class);
    $router->addMiddleware(new LoggerMiddleware($logger));
    
    // Register routes
    $router->addRoute('GET', '/', App\Controllers\HomeController::class, 'index');
    $router->addRoute('GET', '/test', App\Controllers\TestController::class, 'index');
    $router->addRoute('GET', '/test/{id}', App\Controllers\TestController::class, 'show');
    
    return $router;
});

// Create PSR-7 ServerRequest from globals
$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
$request = $creator->fromGlobals();

// Handle request through Router (PSR-15 request handler)
/** @var Router $router */
$router = $container->get(Router::class);
$response = $router->handle($request);

// Send response to browser
http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}
echo $response->getBody();
