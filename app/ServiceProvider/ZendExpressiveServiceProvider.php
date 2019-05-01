<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use Pimple\Container;
use Pimple\Psr11\Container as Psr11Container;
use Pimple\ServiceProviderInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
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
use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Zend\HttpHandlerRunner\RequestHandlerRunner;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Stratigility\MiddlewarePipe;

final class ZendExpressiveServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $psrContainer = new Psr11Container($container);

        $container['zend.expressive.responseFactory'] = function () {
            return function () {
                return new Response();
            };
        };

        $container['zend.expressive.serverRequestFactory'] = function () {
            return function () {
                return ServerRequestFactory::fromGlobals();
            };
        };

        $container[MiddlewareContainer::class] = function () use ($psrContainer) {
            return new MiddlewareContainer($psrContainer);
        };

        $container[MiddlewareFactory::class] = function () use ($container) {
            return new MiddlewareFactory($container[MiddlewareContainer::class]);
        };

        $container[MiddlewarePipe::class] = function () {
            return new MiddlewarePipe();
        };

        $container[FastRouteRouter::class] = function () use ($container) {
            return new FastRouteRouter(null, null, $container['fastroute']);
        };

        $container[RouteCollector::class] = function () use ($container) {
            return new RouteCollector($container[FastRouteRouter::class]);
        };

        $container[EmitterStack::class] = function () {
            $emitterStack = new EmitterStack();
            $emitterStack->push(new SapiEmitter());

            return $emitterStack;
        };

        $container[ServerRequestErrorResponseGenerator::class] = function () use ($container) {
            return new ServerRequestErrorResponseGenerator(
                $container['zend.expressive.responseFactory'],
                $container['debug']
            );
        };

        $container[RequestHandlerRunner::class] = function () use ($container) {
            return new RequestHandlerRunner(
                $container[MiddlewarePipe::class],
                $container[EmitterStack::class],
                $container['zend.expressive.serverRequestFactory'],
                $container[ServerRequestErrorResponseGenerator::class]
            );
        };

        $container[ErrorHandler::class] = function () use ($container) {
            return new ErrorHandler(
                $container['zend.expressive.responseFactory'],
                $container[ErrorResponseGenerator::class]
            );
        };

        $container[ErrorResponseGenerator::class] = function () use ($container) {
            return new ErrorResponseGenerator($container['debug']);
        };

        $container[RouteMiddleware::class] = function () use ($container) {
            return new RouteMiddleware($container[FastRouteRouter::class]);
        };

        $container[MethodNotAllowedMiddleware::class] = function () use ($container) {
            return new MethodNotAllowedMiddleware($container['zend.expressive.responseFactory']);
        };

        $container[DispatchMiddleware::class] = function () {
            return new DispatchMiddleware();
        };

        $container[NotFoundHandler::class] = function () use ($container) {
            return new NotFoundHandler($container['zend.expressive.responseFactory']);
        };
    }
}
