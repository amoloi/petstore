<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\IndexController;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;

/**
 * @covers \App\Controller\IndexController
 */
class IndexControllerTest extends TestCase
{
    use MockByCallsTrait;

    public function testHandle(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Location', 'https://petstore/api')->willReturnSelf(),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(302, '')->willReturn($response),
        ]);

        /** @var RouterInterface|MockObject $router */
        $router = $this->getMockByCalls(RouterInterface::class, [
            Call::create('generateUri')->with('swagger_index', [], [])->willReturn('https://petstore/api'),
        ]);

        $controller = new IndexController($responseFactory, $router);

        self::assertSame($response, $controller->handle($request));
    }
}
