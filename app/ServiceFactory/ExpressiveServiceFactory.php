<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Zend\Expressive\Handler\NotFoundHandler;
use Zend\Expressive\Middleware\ErrorResponseGenerator;
use Zend\Expressive\MiddlewareContainer;
use Zend\Expressive\MiddlewareFactory;
use Zend\Expressive\Response\ServerRequestErrorResponseGenerator;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;
use Zend\Expressive\Router\Middleware\MethodNotAllowedMiddleware;
use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Router\RouteCollector;
use Zend\Expressive\Router\RouterInterface;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Zend\HttpHandlerRunner\RequestHandlerRunner;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Stratigility\MiddlewarePipe;
use Zend\Stratigility\MiddlewarePipeInterface;

final class ExpressiveServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            'zend.expressive.responseFactory' => static function () {
                return static function () {
                    return (new ResponseFactory())->createResponse();
                };
            },
            'zend.expressive.serverRequestFactory' => static function () {
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
            RequestHandlerRunner::class => static function (ContainerInterface $container) {
                $emitterStack = new EmitterStack();
                $emitterStack->push(new SapiEmitter());

                return new RequestHandlerRunner(
                    $container->get(MiddlewarePipeInterface::class),
                    $emitterStack,
                    $container->get('zend.expressive.serverRequestFactory'),
                    new ServerRequestErrorResponseGenerator(
                        $container->get('zend.expressive.responseFactory'),
                        $container->get('debug')
                    )
                );
            },
            ErrorHandler::class => static function (ContainerInterface $container) {
                return new ErrorHandler(
                    $container->get('zend.expressive.responseFactory'),
                    new ErrorResponseGenerator($container->get('debug'))
                );
            },
            RouteMiddleware::class => static function (ContainerInterface $container) {
                return new RouteMiddleware($container->get(RouterInterface::class));
            },
            MethodNotAllowedMiddleware::class => static function (ContainerInterface $container) {
                return new MethodNotAllowedMiddleware($container->get('zend.expressive.responseFactory'));
            },
            DispatchMiddleware::class => static function () {
                return new DispatchMiddleware();
            },
            NotFoundHandler::class => static function (ContainerInterface $container) {
                return new NotFoundHandler($container->get('zend.expressive.responseFactory'));
            },
        ];
    }
}
