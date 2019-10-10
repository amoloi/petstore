<?php

declare(strict_types=1);

namespace App\RequestHandler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Router\RouterInterface;

final class IndexRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        RouterInterface $router
    ) {
        $this->responseFactory = $responseFactory;
        $this->router = $router;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', $this->router->generateUri('swagger_index'))
        ;
    }
}
