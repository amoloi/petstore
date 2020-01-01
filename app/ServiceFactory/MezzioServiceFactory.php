<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use Laminas\HttpHandlerRunner\Emitter\EmitterStack;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Laminas\Stratigility\MiddlewarePipe;
use Laminas\Stratigility\MiddlewarePipeInterface;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\Middleware\ErrorResponseGenerator;
use Mezzio\MiddlewareContainer;
use Mezzio\MiddlewareFactory;
use Mezzio\Response\ServerRequestErrorResponseGenerator;
use Mezzio\Router\FastRouteRouter;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\MethodNotAllowedMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Mezzio\Router\RouteCollector;
use Mezzio\Router\RouterInterface;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class MezzioServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            'mezzio.responseFactory' => static function () {
                return static function () {
                    return (new ResponseFactory())->createResponse();
                };
            },
            'mezzio.serverRequestFactory' => static function () {
                return static function () {
                    return (new ServerRequestFactory())->createFromGlobals();
                };
            },
            MiddlewareFactory::class => static function (ContainerInterface $container) {
                return new MiddlewareFactory(new MiddlewareContainer($container));
            },
            MiddlewarePipeInterface::class => static function () {
                return new MiddlewarePipe();
            },
            RouterInterface::class => static function () {
                return new FastRouteRouter();
            },
            RouteCollector::class => static function (ContainerInterface $container) {
                return new RouteCollector($container->get(RouterInterface::class));
            },
            EmitterStack::class => static function () {
                $emitterStack = new EmitterStack();
                $emitterStack->push(new SapiEmitter());

                return $emitterStack;
            },
            RequestHandlerRunner::class => static function (ContainerInterface $container) {
                return new RequestHandlerRunner(
                    $container->get(MiddlewarePipeInterface::class),
                    $container->get(EmitterStack::class),
                    $container->get('mezzio.serverRequestFactory'),
                    new ServerRequestErrorResponseGenerator(
                        $container->get('mezzio.responseFactory'),
                        $container->get('debug')
                    )
                );
            },
            ErrorHandler::class => static function (ContainerInterface $container) {
                return new ErrorHandler(
                    $container->get('mezzio.responseFactory'),
                    new ErrorResponseGenerator($container->get('debug'))
                );
            },
            RouteMiddleware::class => static function (ContainerInterface $container) {
                return new RouteMiddleware($container->get(RouterInterface::class));
            },
            MethodNotAllowedMiddleware::class => static function (ContainerInterface $container) {
                return new MethodNotAllowedMiddleware($container->get('mezzio.responseFactory'));
            },
            DispatchMiddleware::class => static function () {
                return new DispatchMiddleware();
            },
            NotFoundHandler::class => static function (ContainerInterface $container) {
                return new NotFoundHandler($container->get('mezzio.responseFactory'));
            },
        ];
    }
}
