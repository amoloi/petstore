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
use App\RequestHandler\Api\Swagger\IndexRequestHandler;
use App\RequestHandler\Api\Swagger\YamlRequestHandler;
use App\ServiceFactory\MiddlewareServiceFactory;
use App\ServiceFactory\RequestHandlerServiceFactory;
use App\ServiceFactory\SlimServiceFactory;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\ApiHttp\Middleware\ApiExceptionMiddleware;
use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\ServiceFactory\ConfigServiceFactory;
use Chubbyphp\Container\Container;
use Chubbyphp\Cors\CorsMiddleware;
use Slim\App;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Routing\RouteCollectorProxy;

require __DIR__.'/../vendor/autoload.php';

return static function (string $env) {
    /** @var Container $container */
    $container = (require __DIR__.'/container.php')();
    $container->factories((new MiddlewareServiceFactory())());
    $container->factories((new RequestHandlerServiceFactory())());
    $container->factories((new SlimServiceFactory())());

    // always load this service provider last
    // so that the values of other service providers can be overwritten.
    $container->factories((new ConfigServiceFactory((new ConfigProvider([
        new DevConfig(__DIR__.'/..'),
        new PhpunitConfig(__DIR__.'/..'),
        new ProdConfig(__DIR__.'/..'),
    ]))->get($env)))());

    $web = new App(
        $container->get('api-http.response.factory'),
        $container,
        $container->get(CallableResolverInterface::class),
        $container->get(RouteCollectorInterface::class)
    );

    $web->add(CorsMiddleware::class);
    $web->addErrorMiddleware($container->get('debug'), true, true);

    $web->group('/api', function (RouteCollectorProxy $group): void {
        $group->get('/swagger/index', IndexRequestHandler::class)->setName('swagger_index');
        $group->get('/swagger/yaml', YamlRequestHandler::class)->setName('swagger_yaml');
        $group->get('/ping', PingRequestHandler::class)->setName('ping')
            ->add(ApiExceptionMiddleware::class)
            ->add(AcceptAndContentTypeMiddleware::class)
        ;
        $group->group('/pets', function (RouteCollectorProxy $group): void {
            $group->get('', ListRequestHandler::class.Pet::class)->setName('pet_list');
            $group->post('', CreateRequestHandler::class.Pet::class)->setName('pet_create');
            $group->get('/{id}', ReadRequestHandler::class.Pet::class)->setName('pet_read');
            $group->put('/{id}', UpdateRequestHandler::class.Pet::class)->setName('pet_update');
            $group->delete('/{id}', DeleteRequestHandler::class.Pet::class)->setName('pet_delete');
        })->add(ApiExceptionMiddleware::class)->add(AcceptAndContentTypeMiddleware::class);
    });

    return $web;
};
