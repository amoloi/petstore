<?php

declare(strict_types=1);

namespace App;

use App\Config\DevConfig;
use App\Config\PhpunitConfig;
use App\Config\ProdConfig;
use App\Model\Pet;
use App\RequestHandler\Crud\CreateRequestHandler;
use App\RequestHandler\Crud\DeleteRequestHandler;
use App\RequestHandler\Crud\ListRequestHandler;
use App\RequestHandler\Crud\ReadRequestHandler;
use App\RequestHandler\Crud\UpdateRequestHandler;
use App\RequestHandler\IndexRequestHandler;
use App\RequestHandler\PingRequestHandler;
use App\RequestHandler\Swagger\IndexRequestHandler as SwaggerIndexRequestHandler;
use App\RequestHandler\Swagger\YamlRequestHandler as SwaggerYamlRequestHandler;
use App\ServiceFactory\ExpressiveServiceFactory;
use App\ServiceFactory\MiddlewareServiceFactory;
use App\ServiceFactory\RequestHandlerServiceFactory;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\ServiceFactory\ConfigServiceFactory;
use Chubbyphp\Container\Container;
use Chubbyphp\Cors\CorsMiddleware;
use Zend\Expressive\Application;
use Zend\Expressive\Handler\NotFoundHandler;
use Zend\Expressive\MiddlewareFactory;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;
use Zend\Expressive\Router\Middleware\MethodNotAllowedMiddleware;
use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Router\RouteCollector;
use Zend\HttpHandlerRunner\RequestHandlerRunner;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Stratigility\MiddlewarePipeInterface;

require __DIR__.'/../vendor/autoload.php';

return static function (string $env) {
    /** @var Container $container */
    $container = (require __DIR__.'/container.php')();
    $container->factories((new MiddlewareServiceFactory())());
    $container->factories((new RequestHandlerServiceFactory())());
    $container->factories((new ExpressiveServiceFactory())());

    // always load this service provider last
    // so that the values of other service providers can be overwritten.
    $container->factories((new ConfigServiceFactory((new ConfigProvider([
        new DevConfig(__DIR__.'/..'),
        new PhpunitConfig(__DIR__.'/..'),
        new ProdConfig(__DIR__.'/..'),
    ]))->get($env)))());

    $web = new Application(
        $container->get(MiddlewareFactory::class),
        $container->get(MiddlewarePipeInterface::class),
        $container->get(RouteCollector::class),
        $container->get(RequestHandlerRunner::class)
    );

    $web->pipe(ErrorHandler::class);
    $web->pipe(CorsMiddleware::class);
    $web->pipe(RouteMiddleware::class);
    $web->pipe(MethodNotAllowedMiddleware::class);
    $web->pipe(DispatchMiddleware::class);
    $web->pipe(NotFoundHandler::class);

    $web->get('/', IndexRequestHandler::class, 'index');
    $web->get('/api', SwaggerIndexRequestHandler::class, 'swagger_index');
    $web->get('/api/swagger', SwaggerYamlRequestHandler::class, 'swagger_yml');
    $web->get('/api/ping', [AcceptAndContentTypeMiddleware::class, PingRequestHandler::class], 'ping');
    $web->get('/api/pets', [AcceptAndContentTypeMiddleware::class, ListRequestHandler::class.Pet::class], 'pet_list');
    $web->post(
        '/api/pets',
        [AcceptAndContentTypeMiddleware::class, CreateRequestHandler::class.Pet::class],
        'pet_create'
    );
    $web->get(
        '/api/pets/{id}',
        [AcceptAndContentTypeMiddleware::class, ReadRequestHandler::class.Pet::class],
        'pet_read'
    );
    $web->put(
        '/api/pets/{id}',
        [AcceptAndContentTypeMiddleware::class, UpdateRequestHandler::class.Pet::class],
        'pet_update'
    );
    $web->delete(
        '/api/pets/{id}',
        [AcceptAndContentTypeMiddleware::class, DeleteRequestHandler::class.Pet::class],
        'pet_delete'
    );

    return $web;
};
