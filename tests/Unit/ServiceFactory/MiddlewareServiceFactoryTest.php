<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\ServiceFactory\MiddlewareServiceFactory;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginExact;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * @covers \App\ServiceFactory\MiddlewareServiceFactory
 *
 * @internal
 */
final class MiddlewareServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new MiddlewareServiceFactory())();

        self::assertCount(2, $factories);
    }

    public function testAcceptAndContentTypeMiddleware(): void
    {
        /** @var AcceptNegotiatorInterface|MockObject $acceptNegotiator */
        $acceptNegotiator = $this->getMockByCalls(AcceptNegotiatorInterface::class);

        /** @var ContentTypeNegotiatorInterface|MockObject $contentTypeNegotiator */
        $contentTypeNegotiator = $this->getMockByCalls(ContentTypeNegotiatorInterface::class);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('negotiator.acceptNegotiator')->willReturn($acceptNegotiator),
            Call::create('get')->with('negotiator.contentTypeNegotiator')->willReturn($contentTypeNegotiator),
            Call::create('get')->with('api-http.response.manager')->willReturn($responseManager),
        ]);

        $factories = (new MiddlewareServiceFactory())();

        self::assertArrayHasKey(AcceptAndContentTypeMiddleware::class, $factories);

        self::assertInstanceOf(
            AcceptAndContentTypeMiddleware::class,
            $factories[AcceptAndContentTypeMiddleware::class]($container)
        );
    }

    public function testCorsMiddleware(): void
    {
        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('cors')->willReturn([
                'allow-origin' => ['https://myproject.com' => AllowOriginExact::class],
                'allow-methods' => ['GET'],
                'allow-headers' => [
                    'Accept',
                    'Content-Type',
                ],
                'allow-credentials' => false,
                'expose-headers' => [],
                'max-age' => 7200,
            ]),
            Call::create('get')->with('api-http.response.factory')->willReturn($responseFactory),
        ]);

        $factories = (new MiddlewareServiceFactory())();

        self::assertArrayHasKey(CorsMiddleware::class, $factories);

        self::assertInstanceOf(
            CorsMiddleware::class,
            $factories[CorsMiddleware::class]($container)
        );
    }
}
