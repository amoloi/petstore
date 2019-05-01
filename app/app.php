<?php

declare(strict_types=1);

namespace App;

use App\Controller\Crud\CreateController;
use App\Controller\Crud\DeleteController;
use App\Controller\Crud\ListController;
use App\Controller\Crud\ReadController;
use App\Controller\Crud\UpdateController;
use App\Controller\IndexController;
use App\Controller\Swagger\IndexController as SwaggerIndexController;
use App\Controller\Swagger\YamlController as SwaggerYamlController;
use App\Middleware\AcceptAndContentTypeMiddleware;
use App\Model\Pet;
use App\ServiceProvider\ControllerServiceProvider;
use App\ServiceProvider\MiddlewareServiceProvider;
use App\ServiceProvider\ZendExpressiveServiceProvider;
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

$app = new Application(
    $container[MiddlewareFactory::class],
    $container[MiddlewarePipe::class],
    $container[RouteCollector::class],
    $container[RequestHandlerRunner::class]
);

$app->pipe(ErrorHandler::class);
$app->pipe(RouteMiddleware::class);
$app->pipe(MethodNotAllowedMiddleware::class);
$app->pipe(DispatchMiddleware::class);
$app->pipe(NotFoundHandler::class);

$app->get('/', IndexController::class, 'index');
$app->get('/api', SwaggerIndexController::class, 'swagger_index');
$app->get('/api/swagger.yml', SwaggerYamlController::class, 'swagger_yml');
$app->get('/api/ping', [AcceptAndContentTypeMiddleware::class, SwaggerYamlController::class], 'ping');
$app->get('/api/pets', [AcceptAndContentTypeMiddleware::class, ListController::class.Pet::class], 'pet_list');
$app->post('/api/pets', [AcceptAndContentTypeMiddleware::class, CreateController::class.Pet::class], 'pet_create');
$app->get('/api/pets/{id}', [AcceptAndContentTypeMiddleware::class, ReadController::class.Pet::class], 'pet_read');
$app->put('/api/pets/{id}', [AcceptAndContentTypeMiddleware::class, UpdateController::class.Pet::class], 'pet_update');
$app->delete('/api/pets/{id}', [AcceptAndContentTypeMiddleware::class, DeleteController::class.Pet::class], 'pet_delete');

return $app;
