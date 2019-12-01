<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\ServiceFactory\SlimServiceFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteParserInterface;

/**
 * @covers \App\ServiceFactory\SlimServiceFactory
 *
 * @internal
 */
final class SlimServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new SlimServiceFactory())();

        self::assertCount(3, $factories);
    }

    public function testCallableResolver(): void
    {
        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factories = (new SlimServiceFactory())();

        self::assertArrayHasKey(CallableResolverInterface::class, $factories);

        self::assertInstanceOf(
            CallableResolverInterface::class,
            $factories[CallableResolverInterface::class]($container)
        );
    }

    public function testRouteCollector(): void
    {
        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var CallableResolverInterface|MockObject $callableResolver */
        $callableResolver = $this->getMockByCalls(CallableResolverInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('api-http.response.factory')->willReturn($responseFactory),
            Call::create('get')->with(CallableResolverInterface::class)->willReturn($callableResolver),
            Call::create('get')->with('routerCacheFile')->willReturn(sys_get_temp_dir().'/router-'.uniqid().uniqid()),
        ]);

        $factories = (new SlimServiceFactory())();

        self::assertArrayHasKey(RouteCollectorInterface::class, $factories);

        self::assertInstanceOf(
            RouteCollectorInterface::class,
            $factories[RouteCollectorInterface::class]($container)
        );
    }

    public function testRouteParser(): void
    {
        /** @var RouteParserInterface|MockObject $routeParser */
        $routeParser = $this->getMockByCalls(RouteParserInterface::class);

        /** @var RouteCollectorInterface|MockObject $routeCollector */
        $routeCollector = $this->getMockByCalls(RouteCollectorInterface::class, [
            Call::create('getRouteParser')->with()->willReturn($routeParser),
        ]);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(RouteCollectorInterface::class)->willReturn($routeCollector),
        ]);

        $factories = (new SlimServiceFactory())();

        self::assertArrayHasKey(RouteParserInterface::class, $factories);

        self::assertSame(
            $routeParser,
            $factories[RouteParserInterface::class]($container)
        );
    }
}
