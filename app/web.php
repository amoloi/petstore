<?php

declare(strict_types=1);

namespace App;

use App\Config\DevConfig;
use App\Config\PhpunitConfig;
use App\Config\ProdConfig;
use App\Model\Pet;
use App\Peak\HandlerResolver;
use App\Peak\Route;
use App\RequestHandler\Crud\CreateRequestHandler;
use App\RequestHandler\Crud\DeleteRequestHandler;
use App\RequestHandler\Crud\ListRequestHandler;
use App\RequestHandler\Crud\ReadRequestHandler;
use App\RequestHandler\Crud\UpdateRequestHandler;
use App\RequestHandler\IndexRequestHandler;
use App\RequestHandler\PingRequestHandler;
use App\RequestHandler\Swagger\IndexRequestHandler as SwaggerIndexRequestHandler;
use App\RequestHandler\Swagger\YamlRequestHandler as SwaggerYamlRequestHandler;
use App\ServiceFactory\MiddlewareServiceFactory;
use App\ServiceFactory\RequestHandlerServiceFactory;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\ServiceFactory\ConfigServiceFactory;
use Chubbyphp\Container\Container;
use Chubbyphp\Cors\CorsMiddleware;
use Peak\Http\Request\PreRoute;
use Peak\Http\Stack;

require __DIR__.'/../vendor/autoload.php';

return static function (string $env) {
    /** @var Container $container */
    $container = (require __DIR__.'/container.php')();
    $container->factories((new MiddlewareServiceFactory())());
    $container->factories((new RequestHandlerServiceFactory())());

    // always load this service provider last
    // so that the values of other service providers can be overwritten.
    $container->factories((new ConfigServiceFactory((new ConfigProvider([
        new DevConfig(__DIR__.'/..'),
        new PhpunitConfig(__DIR__.'/..'),
        new ProdConfig(__DIR__.'/..'),
    ]))->get($env)))());

    $hr = new HandlerResolver($container);

    return new Stack([
        CorsMiddleware::class,
        new Route('GET', '/', new Stack([IndexRequestHandler::class], $hr)),
        new PreRoute('/api', new Stack([
            new Route('GET', '/api', new Stack([SwaggerIndexRequestHandler::class], $hr)),
            new Route('GET', '/api/swagger', new Stack([SwaggerYamlRequestHandler::class], $hr)),
            new Route('GET', '/api/ping', new Stack([
                AcceptAndContentTypeMiddleware::class,
                PingRequestHandler::class,
            ], $hr)),
            new PreRoute('/api/pets', new Stack([
                AcceptAndContentTypeMiddleware::class,
                new Route('GET', '/api/pets', new Stack([ListRequestHandler::class.Pet::class], $hr)),
                new Route('POST', '/api/pets', new Stack([CreateRequestHandler::class.Pet::class], $hr)),
                new Route('GET', '/api/pets/{id}', new Stack([ReadRequestHandler::class.Pet::class], $hr)),
                new Route('PUT', '/api/pets/{id}', new Stack([UpdateRequestHandler::class.Pet::class], $hr)),
                new Route('DELETE', '/api/pets/{id}', new Stack([DeleteRequestHandler::class.Pet::class], $hr)),
            ], $hr)),
        ], $hr)),
    ], $hr);
};
