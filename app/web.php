<?php

declare(strict_types=1);

namespace App;

use App\Config\DevConfig;
use App\Config\PhpunitConfig;
use App\Config\ProdConfig;
use App\Model\Pet;
use App\Peak\HandlerResolver;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use App\RequestHandler\Api\PingRequestHandler;
use App\RequestHandler\Api\Swagger\IndexRequestHandler;
use App\RequestHandler\Api\Swagger\YamlRequestHandler;
use App\ServiceFactory\MiddlewareServiceFactory;
use App\ServiceFactory\RequestHandlerServiceFactory;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\ServiceFactory\ConfigServiceFactory;
use Chubbyphp\Container\Container;
use Chubbyphp\Cors\CorsMiddleware;
use Peak\Bedrock\Http\Application;
use Peak\Bedrock\Kernel;

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

    $app = new Application(
        new Kernel($env, $container),
        new HandlerResolver($container)
    );

    $app
        ->stack(CorsMiddleware::class)
        ->group('/api', function () use ($app): void {
            $app
                ->get('/swagger/index', IndexRequestHandler::class)
                ->get('/swagger/yaml', YamlRequestHandler::class)
                ->get('/ping', [
                    AcceptAndContentTypeMiddleware::class,
                    PingRequestHandler::class,
                ])
                ->group('/pets', function () use ($app): void {
                    $app
                        ->stack(AcceptAndContentTypeMiddleware::class)
                        ->get('', ListRequestHandler::class.Pet::class)
                        ->post('', CreateRequestHandler::class.Pet::class)
                        ->get('/{id}', ReadRequestHandler::class.Pet::class)
                        ->put('/{id}', UpdateRequestHandler::class.Pet::class)
                        ->delete('/{id}', DeleteRequestHandler::class.Pet::class)
                    ;
                })
            ;
        })
    ;

    return $app;
};
