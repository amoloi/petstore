<?php

declare(strict_types=1);

namespace App\OAuth2\RequestHandler;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthorizationHandler implements RequestHandlerInterface
{
    /**
     * @var AuthorizationServer
     */
    private $server;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct(AuthorizationServer $server, ResponseFactoryInterface $responseFactory)
    {
        $this->server = $server;
        $this->responseFactory = $responseFactory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $authRequest = $request->getAttribute(AuthorizationRequest::class);

        return $this->server->completeAuthorizationRequest($authRequest, $this->responseFactory->createResponse());
    }
}
