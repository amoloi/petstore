<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\ServiceFactory\ApiHttpServiceFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @covers \App\ServiceFactory\ApiHttpServiceFactory
 *
 * @internal
 */
final class ApiHttpServiceFactoryTest extends TestCase
{
    public function testFactories(): void
    {
        $factories = (new ApiHttpServiceFactory())();

        self::assertCount(2, $factories);
    }

    public function testResponseFactory(): void
    {
        $factories = (new ApiHttpServiceFactory())();

        self::assertArrayHasKey('api-http.response.factory', $factories);

        self::assertInstanceOf(ResponseFactoryInterface::class, $factories['api-http.response.factory']());
    }

    public function testStreamFactory(): void
    {
        $factories = (new ApiHttpServiceFactory())();

        self::assertArrayHasKey('api-http.stream.factory', $factories);

        self::assertInstanceOf(StreamFactoryInterface::class, $factories['api-http.stream.factory']());
    }
}
