<?php

declare(strict_types=1);

namespace App\Tests\Unit\Peak;

use App\Peak\HandlerResolver;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\Peak\HandlerResolver
 *
 * @internal
 */
final class HandlerResolverTest extends TestCase
{
    use MockByCallsTrait;

    public function testResolveWithContainer(): void
    {
        $handler = new \stdClass();

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('container:key')->willReturn($handler),
        ]);

        $handlerResolver = new HandlerResolver($container);

        self::assertSame($handler, $handlerResolver->resolve('container:key'));
    }

    public function testResolveWithoutContainer(): void
    {
        $handlerResolver = new HandlerResolver(null);

        self::assertInstanceOf(\stdClass::class, $handlerResolver->resolve(\stdClass::class));
    }
}
