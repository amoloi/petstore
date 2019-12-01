<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\ServiceFactory\ExpressiveServiceFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Handler\NotFoundHandler;
use Zend\Expressive\MiddlewareFactory;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;
use Zend\Expressive\Router\Middleware\MethodNotAllowedMiddleware;
use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Router\RouteCollector;
use Zend\Expressive\Router\RouterInterface;
use Zend\HttpHandlerRunner\RequestHandlerRunner;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Stratigility\MiddlewarePipeInterface;

/**
 * @covers \App\ServiceFactory\ExpressiveServiceFactory
 *
 * @internal
 */
final class ExpressiveServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new ExpressiveServiceFactory())();

        self::assertCount(12, $factories);
    }

    public function testResponseFactory(): void
    {
        $factories = (new ExpressiveServiceFactory())();

        self::assertArrayHasKey('zend.expressive.responseFactory', $factories);

        $responseFactory = $factories['zend.expressive.responseFactory']();

        self::assertInstanceOf(\Closure::class, $responseFactory);

        self::assertInstanceOf(ResponseInterface::class, $responseFactory());
    }

    public function testServerRequestFactory(): void
    {
        $factories = (new ExpressiveServiceFactory())();

        self::assertArrayHasKey('zend.expressive.serverRequestFactory', $factories);

        $serverRequestFactory = $factories['zend.expressive.serverRequestFactory']();

        self::assertInstanceOf(\Closure::class, $serverRequestFactory);

        self::assertInstanceOf(ServerRequestInterface::class, $serverRequestFactory());
    }

    public function testMiddlewareFactory(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factories = (new ExpressiveServiceFactory())();

        self::assertInstanceOf(MiddlewareFactory::class, $factories[MiddlewareFactory::class]($container));
    }

    public function testMiddlewarePipe(): void
    {
        $factories = (new ExpressiveServiceFactory())();

        self::assertInstanceOf(MiddlewarePipeInterface::class, $factories[MiddlewarePipeInterface::class]());
    }

    public function testRouter(): void
    {
        $factories = (new ExpressiveServiceFactory())();

        self::assertInstanceOf(RouterInterface::class, $factories[RouterInterface::class]());
    }

    public function testRouteCollector(): void
    {
        /** @var RouterInterface $router */
        $router = $this->getMockByCalls(RouterInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(RouterInterface::class)->willReturn($router),
        ]);

        $factories = (new ExpressiveServiceFactory())();

        self::assertInstanceOf(RouteCollector::class, $factories[RouteCollector::class]($container));
    }

    public function testRequestHandlerRunner(): void
    {
        /** @var MiddlewarePipeInterface $middlewarePipe */
        $middlewarePipe = $this->getMockByCalls(MiddlewarePipeInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(MiddlewarePipeInterface::class)->willReturn($middlewarePipe),
            Call::create('get')->with('zend.expressive.serverRequestFactory')->willReturn(function (): void {}),
            Call::create('get')->with('zend.expressive.responseFactory')->willReturn(function (): void {}),
            Call::create('get')->with('debug')->willReturn(false),
        ]);

        $factories = (new ExpressiveServiceFactory())();

        self::assertInstanceOf(RequestHandlerRunner::class, $factories[RequestHandlerRunner::class]($container));
    }

    public function testErrorHandler(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('zend.expressive.responseFactory')->willReturn(function (): void {}),
            Call::create('get')->with('debug')->willReturn(false),
        ]);

        $factories = (new ExpressiveServiceFactory())();

        self::assertInstanceOf(ErrorHandler::class, $factories[ErrorHandler::class]($container));
    }

    public function testRouteMiddleware(): void
    {
        /** @var RouterInterface $router */
        $router = $this->getMockByCalls(RouterInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(RouterInterface::class)->willReturn($router),
        ]);

        $factories = (new ExpressiveServiceFactory())();

        self::assertInstanceOf(RouteMiddleware::class, $factories[RouteMiddleware::class]($container));
    }

    public function testMethodNotAllowedMiddleware(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('zend.expressive.responseFactory')->willReturn(function (): void {}),
        ]);

        $factories = (new ExpressiveServiceFactory())();

        self::assertInstanceOf(
            MethodNotAllowedMiddleware::class,
            $factories[MethodNotAllowedMiddleware::class]($container)
        );
    }

    public function testDispatchMiddleware(): void
    {
        $factories = (new ExpressiveServiceFactory())();

        self::assertInstanceOf(DispatchMiddleware::class, $factories[DispatchMiddleware::class]());
    }

    public function testNotFoundHandler(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('zend.expressive.responseFactory')->willReturn(function (): void {}),
        ]);

        $factories = (new ExpressiveServiceFactory())();

        self::assertInstanceOf(
            NotFoundHandler::class,
            $factories[NotFoundHandler::class]($container)
        );
    }
}
