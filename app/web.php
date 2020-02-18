<?php

declare(strict_types=1);

namespace App;

use App\Config\DevConfig;
use App\Config\PhpunitConfig;
use App\Config\ProdConfig;
use App\Model\Pet;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use App\RequestHandler\Api\PingRequestHandler;
use App\RequestHandler\Api\Swagger\IndexRequestHandler as SwaggerIndexRequestHandler;
use App\RequestHandler\Api\Swagger\YamlRequestHandler as SwaggerYamlRequestHandler;
use App\RequestHandler\IndexRequestHandler;
use App\ServiceFactory\MezzioServiceFactory;
use App\ServiceFactory\MiddlewareServiceFactory;
use App\ServiceFactory\RequestHandlerServiceFactory;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\ServiceFactory\ConfigServiceFactory;
use Chubbyphp\Container\Container;
use Chubbyphp\Cors\CorsMiddleware;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Laminas\Stratigility\MiddlewarePipeInterface;
use Mezzio\Application;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\MiddlewareFactory;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\MethodNotAllowedMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Mezzio\Router\RouteCollector;

require __DIR__.'/../vendor/autoload.php';

return static function (string $env) {
    /** @var Container $container */
    $container = (require __DIR__.'/container.php')();
    $container->factories((new MiddlewareServiceFactory())());
    $container->factories((new RequestHandlerServiceFactory())());
    $container->factories((new MezzioServiceFactory())());

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
    $web->get('/api/swagger/index', SwaggerIndexRequestHandler::class, 'swagger_index');
    $web->get('/api/swagger/yml', SwaggerYamlRequestHandler::class, 'swagger_yml');
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
