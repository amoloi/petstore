<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Model\Pet;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use App\RequestHandler\Api\Crud\UpdateRequestHandler;
use App\RequestHandler\Api\PingRequestHandler;
use App\RequestHandler\Api\Swagger\IndexRequestHandler;
use App\RequestHandler\Api\Swagger\YamlRequestHandler;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\Router\FastRouteRouter;
use Chubbyphp\Framework\Router\Group;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouterInterface;
use Psr\Container\ContainerInterface;

final class ChubbyphpFrameworkServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            ExceptionMiddleware::class => static function (ContainerInterface $container) {
                return new ExceptionMiddleware(
                    $container->get('api-http.response.factory'),
                    $container->get('debug'),
                    $container->get('logger')
                );
            },
            RouterMiddleware::class => static function (ContainerInterface $container) {
                return new RouterMiddleware(
                    $container->get(RouterInterface::class),
                    $container->get('api-http.response.factory')
                );
            },
            RouterInterface::class => static function (ContainerInterface $container) {
                $acceptAndContentType = new LazyMiddleware($container, AcceptAndContentTypeMiddleware::class);

                $ping = new LazyRequestHandler($container, PingRequestHandler::class);
                $index = new LazyRequestHandler($container, IndexRequestHandler::class);
                $yaml = new LazyRequestHandler($container, YamlRequestHandler::class);
                $petList = new LazyRequestHandler($container, ListRequestHandler::class.Pet::class);
                $petCreate = new LazyRequestHandler($container, CreateRequestHandler::class.Pet::class);
                $petRead = new LazyRequestHandler($container, ReadRequestHandler::class.Pet::class);
                $petUpdate = new LazyRequestHandler($container, UpdateRequestHandler::class.Pet::class);
                $petDelete = new LazyRequestHandler($container, DeleteRequestHandler::class.Pet::class);

                return new FastRouteRouter(
                    Group::create('')
                        ->group(
                            Group::create('/api')
                            ->route(Route::get('/ping', 'ping', $ping)->middleware($acceptAndContentType))
                                ->route(Route::get('/swagger/index', 'swagger_index', $index))
                                ->route(Route::get('/swagger/yaml', 'swagger_yaml', $yaml))
                                ->group(
                                    Group::create('/pets')
                                        ->route(Route::get('', 'pet_list', $petList))
                                        ->route(Route::post('', 'pet_create', $petCreate))
                                        ->route(Route::get('/{id}', 'pet_read', $petRead))
                                        ->route(Route::put('/{id}', 'pet_update', $petUpdate))
                                        ->route(Route::delete('/{id}', 'pet_delete', $petDelete))
                                        ->middleware($acceptAndContentType)
                                )
                        )
                        ->getRoutes(),
                    $container->get('routerCacheFile'));
            },
        ];
    }
}