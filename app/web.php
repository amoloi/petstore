<?php

declare(strict_types=1);

namespace App;

use App\Controller\Crud\CreateController;
use App\Controller\Crud\DeleteController;
use App\Controller\Crud\ListController;
use App\Controller\Crud\ReadController;
use App\Controller\Crud\UpdateController;
use App\Controller\IndexController;
use App\Controller\PingController;
use App\Controller\Swagger\IndexController as SwaggerIndexController;
use App\Controller\Swagger\YamlController as SwaggerYamlController;
use App\Model\Pet;
use App\ServiceProvider\ControllerServiceProvider;
use App\ServiceProvider\MiddlewareServiceProvider;
use App\ServiceProvider\ZendExpressiveServiceProvider;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Pimple\Container;
use Zend\Expressive\Application;
use Zend\Expressive\Handler\NotFoundHandler;
use Zend\Expressive\MiddlewareFactory;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;
use Zend\Expressive\Router\Middleware\MethodNotAllowedMiddleware;
use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Router\RouteCollector;
use Zend\HttpHandlerRunner\RequestHandlerRunner;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Stratigility\MiddlewarePipe;

require __DIR__.'/bootstrap.php';

/** @var Container $container */
$container = require __DIR__.'/container.php';
$container->register(new ControllerServiceProvider());
$container->register(new MiddlewareServiceProvider());
$container->register(new ZendExpressiveServiceProvider());

$web = new Application(
    $container[MiddlewareFactory::class],
    $container[MiddlewarePipe::class],
    $container[RouteCollector::class],
    $container[RequestHandlerRunner::class]
);

$web->pipe(ErrorHandler::class);
$web->pipe(RouteMiddleware::class);
$web->pipe(MethodNotAllowedMiddleware::class);
$web->pipe(DispatchMiddleware::class);
$web->pipe(NotFoundHandler::class);

$web->get('/', IndexController::class, 'index');
$web->get('/api', SwaggerIndexController::class, 'swagger_index');
$web->get('/api/swagger', SwaggerYamlController::class, 'swagger_yml');
$web->get('/api/ping', [AcceptAndContentTypeMiddleware::class, PingController::class], 'ping');
$web->get('/api/pets', [AcceptAndContentTypeMiddleware::class, ListController::class.Pet::class], 'pet_list');
$web->post('/api/pets', [AcceptAndContentTypeMiddleware::class, CreateController::class.Pet::class], 'pet_create');
$web->get('/api/pets/{id}', [AcceptAndContentTypeMiddleware::class, ReadController::class.Pet::class], 'pet_read');
$web->put('/api/pets/{id}', [AcceptAndContentTypeMiddleware::class, UpdateController::class.Pet::class], 'pet_update');
$web->delete('/api/pets/{id}', [AcceptAndContentTypeMiddleware::class, DeleteController::class.Pet::class], 'pet_delete');

return $web;
