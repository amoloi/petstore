<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;
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
    public function register(Container $container): void
    {
        $container['zend.expressive.responseFactory'] = static function () {
            return static function () {
                return new Response();
            };
        };

        $container['zend.expressive.serverRequestFactory'] = static function () {
            return static function () {
                return ServerRequestFactory::fromGlobals();
            };
        };

        $container[MiddlewareContainer::class] = static function () use ($container) {
            return new MiddlewareContainer($container[PsrContainer::class]);
        };

        $container[MiddlewareFactory::class] = static function () use ($container) {
            return new MiddlewareFactory($container[MiddlewareContainer::class]);
        };

        $container[MiddlewarePipe::class] = static function () {
            return new MiddlewarePipe();
        };

        $container[FastRouteRouter::class] = static function () use ($container) {
            return new FastRouteRouter(null, null, $container['fastroute']);
        };

        $container[RouteCollector::class] = static function () use ($container) {
            return new RouteCollector($container[FastRouteRouter::class]);
        };

        $container[EmitterStack::class] = static function () {
            $emitterStack = new EmitterStack();
            $emitterStack->push(new SapiEmitter());

            return $emitterStack;
        };

        $container[ServerRequestErrorResponseGenerator::class] = static function () use ($container) {
            return new ServerRequestErrorResponseGenerator(
                $container['zend.expressive.responseFactory'],
                $container['debug']
            );
        };

        $container[RequestHandlerRunner::class] = static function () use ($container) {
            return new RequestHandlerRunner(
                $container[MiddlewarePipe::class],
                $container[EmitterStack::class],
                $container['zend.expressive.serverRequestFactory'],
                $container[ServerRequestErrorResponseGenerator::class]
            );
        };

        $container[ErrorHandler::class] = static function () use ($container) {
            return new ErrorHandler(
                $container['zend.expressive.responseFactory'],
                $container[ErrorResponseGenerator::class]
            );
        };

        $container[ErrorResponseGenerator::class] = static function () use ($container) {
            return new ErrorResponseGenerator($container['debug']);
        };

        $container[RouteMiddleware::class] = static function () use ($container) {
            return new RouteMiddleware($container[FastRouteRouter::class]);
        };

        $container[MethodNotAllowedMiddleware::class] = static function () use ($container) {
            return new MethodNotAllowedMiddleware($container['zend.expressive.responseFactory']);
        };

        $container[DispatchMiddleware::class] = static function () {
            return new DispatchMiddleware();
        };

        $container[NotFoundHandler::class] = static function () use ($container) {
            return new NotFoundHandler($container['zend.expressive.responseFactory']);
        };
    }
}
