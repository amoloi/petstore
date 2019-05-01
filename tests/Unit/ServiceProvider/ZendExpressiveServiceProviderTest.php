<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\ServiceProvider\ZendExpressiveServiceProvider;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Zend\Expressive\MiddlewareContainer;
use Zend\Expressive\MiddlewareFactory;
use Zend\Stratigility\MiddlewarePipe;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\RouteCollector;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use Zend\Expressive\Response\ServerRequestErrorResponseGenerator;
use Zend\HttpHandlerRunner\RequestHandlerRunner;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Expressive\Middleware\ErrorResponseGenerator;
use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Router\Middleware\MethodNotAllowedMiddleware;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;
use Zend\Expressive\Handler\NotFoundHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\ServiceProvider\ZendExpressiveServiceProvider
 */
final class ZendExpressiveServiceProviderTest extends TestCase
{
    public function testRegister(): void
    {
        $container = new Container([
            'debug' => true,
            'fastroute' => [],
        ]);

        $serviceProvider = new ZendExpressiveServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey('zend.expressive.responseFactory', $container);
        self::assertArrayHasKey('zend.expressive.serverRequestFactory', $container);
        self::assertArrayHasKey(MiddlewareContainer::class, $container);
        self::assertArrayHasKey(MiddlewareFactory::class, $container);
        self::assertArrayHasKey(MiddlewarePipe::class, $container);
        self::assertArrayHasKey(FastRouteRouter::class, $container);
        self::assertArrayHasKey(RouteCollector::class, $container);
        self::assertArrayHasKey(EmitterStack::class, $container);
        self::assertArrayHasKey(ServerRequestErrorResponseGenerator::class, $container);
        self::assertArrayHasKey(RequestHandlerRunner::class, $container);
        self::assertArrayHasKey(ErrorHandler::class, $container);
        self::assertArrayHasKey(ErrorResponseGenerator::class, $container);
        self::assertArrayHasKey(RouteMiddleware::class, $container);
        self::assertArrayHasKey(MethodNotAllowedMiddleware::class, $container);
        self::assertArrayHasKey(DispatchMiddleware::class, $container);
        self::assertArrayHasKey(NotFoundHandler::class, $container);

        $responseFactory = $container['zend.expressive.responseFactory'];
        $serverRequestFactory = $container['zend.expressive.serverRequestFactory'];

        self::assertInstanceOf(\Closure::class, $responseFactory);
        self::assertInstanceOf(\Closure::class, $serverRequestFactory);

        self::assertInstanceOf(ResponseInterface::class, $responseFactory());
        self::assertInstanceOf(ServerRequestInterface::class, $serverRequestFactory());

        self::assertInstanceOf(MiddlewareContainer::class, $container[MiddlewareContainer::class]);
        self::assertInstanceOf(MiddlewareFactory::class, $container[MiddlewareFactory::class]);
        self::assertInstanceOf(MiddlewarePipe::class, $container[MiddlewarePipe::class]);
        self::assertInstanceOf(FastRouteRouter::class, $container[FastRouteRouter::class]);
        self::assertInstanceOf(RouteCollector::class, $container[RouteCollector::class]);
        self::assertInstanceOf(EmitterStack::class, $container[EmitterStack::class]);
        self::assertInstanceOf(ServerRequestErrorResponseGenerator::class, $container[ServerRequestErrorResponseGenerator::class]);
        self::assertInstanceOf(RequestHandlerRunner::class, $container[RequestHandlerRunner::class]);
        self::assertInstanceOf(ErrorHandler::class, $container[ErrorHandler::class]);
        self::assertInstanceOf(ErrorResponseGenerator::class, $container[ErrorResponseGenerator::class]);
        self::assertInstanceOf(RouteMiddleware::class, $container[RouteMiddleware::class]);
        self::assertInstanceOf(MethodNotAllowedMiddleware::class, $container[MethodNotAllowedMiddleware::class]);
        self::assertInstanceOf(DispatchMiddleware::class, $container[DispatchMiddleware::class]);
        self::assertInstanceOf(NotFoundHandler::class, $container[NotFoundHandler::class]);
    }
}
