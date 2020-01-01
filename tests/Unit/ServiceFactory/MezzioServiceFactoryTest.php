<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\ServiceFactory\MezzioServiceFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Laminas\HttpHandlerRunner\Emitter\EmitterStack;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Laminas\Stratigility\MiddlewarePipeInterface;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\MiddlewareFactory;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\MethodNotAllowedMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Mezzio\Router\RouteCollector;
use Mezzio\Router\RouterInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\ServiceFactory\MezzioServiceFactory
 *
 * @internal
 */
final class MezzioServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new MezzioServiceFactory())();

        self::assertCount(13, $factories);
    }

    public function testResponseFactory(): void
    {
        $factories = (new MezzioServiceFactory())();

        self::assertArrayHasKey('mezzio.responseFactory', $factories);

        $responseFactory = $factories['mezzio.responseFactory']();

        self::assertInstanceOf(\Closure::class, $responseFactory);

        self::assertInstanceOf(ResponseInterface::class, $responseFactory());
    }

    public function testServerRequestFactory(): void
    {
        $factories = (new MezzioServiceFactory())();

        self::assertArrayHasKey('mezzio.serverRequestFactory', $factories);

        $serverRequestFactory = $factories['mezzio.serverRequestFactory']();

        self::assertInstanceOf(\Closure::class, $serverRequestFactory);

        self::assertInstanceOf(ServerRequestInterface::class, $serverRequestFactory());
    }

    public function testMiddlewareFactory(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factories = (new MezzioServiceFactory())();

        self::assertInstanceOf(MiddlewareFactory::class, $factories[MiddlewareFactory::class]($container));
    }

    public function testMiddlewarePipe(): void
    {
        $factories = (new MezzioServiceFactory())();

        self::assertInstanceOf(MiddlewarePipeInterface::class, $factories[MiddlewarePipeInterface::class]());
    }

    public function testRouter(): void
    {
        $factories = (new MezzioServiceFactory())();

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

        $factories = (new MezzioServiceFactory())();

        self::assertInstanceOf(RouteCollector::class, $factories[RouteCollector::class]($container));
    }

    public function testEmitterStack(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factories = (new MezzioServiceFactory())();

        /** @var EmitterStack $emitterStack */
        $emitterStack = $factories[EmitterStack::class]($container);

        self::assertInstanceOf(EmitterStack::class, $emitterStack);

        self::assertInstanceOf(SapiEmitter::class, $emitterStack->offsetGet($emitterStack->key()));
    }

    public function testRequestHandlerRunner(): void
    {
        /** @var MiddlewarePipeInterface $middlewarePipe */
        $middlewarePipe = $this->getMockByCalls(MiddlewarePipeInterface::class);

        /** @var EmitterStack $emitterStack */
        $emitterStack = $this->getMockByCalls(EmitterStack::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(MiddlewarePipeInterface::class)->willReturn($middlewarePipe),
            Call::create('get')->with(EmitterStack::class)->willReturn($emitterStack),
            Call::create('get')->with('mezzio.serverRequestFactory')->willReturn(function (): void {}),
            Call::create('get')->with('mezzio.responseFactory')->willReturn(function (): void {}),
            Call::create('get')->with('debug')->willReturn(false),
        ]);

        $factories = (new MezzioServiceFactory())();

        self::assertInstanceOf(RequestHandlerRunner::class, $factories[RequestHandlerRunner::class]($container));
    }

    public function testErrorHandler(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('mezzio.responseFactory')->willReturn(function (): void {}),
            Call::create('get')->with('debug')->willReturn(false),
        ]);

        $factories = (new MezzioServiceFactory())();

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

        $factories = (new MezzioServiceFactory())();

        self::assertInstanceOf(RouteMiddleware::class, $factories[RouteMiddleware::class]($container));
    }

    public function testMethodNotAllowedMiddleware(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('mezzio.responseFactory')->willReturn(function (): void {}),
        ]);

        $factories = (new MezzioServiceFactory())();

        self::assertInstanceOf(
            MethodNotAllowedMiddleware::class,
            $factories[MethodNotAllowedMiddleware::class]($container)
        );
    }

    public function testDispatchMiddleware(): void
    {
        $factories = (new MezzioServiceFactory())();

        self::assertInstanceOf(DispatchMiddleware::class, $factories[DispatchMiddleware::class]());
    }

    public function testNotFoundHandler(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('mezzio.responseFactory')->willReturn(function (): void {}),
        ]);

        $factories = (new MezzioServiceFactory())();

        self::assertInstanceOf(
            NotFoundHandler::class,
            $factories[NotFoundHandler::class]($container)
        );
    }
}
