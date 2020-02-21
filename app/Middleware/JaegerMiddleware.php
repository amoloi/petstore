<?php

declare(strict_types=1);

namespace App\Middleware;

use Jaeger\Config;
use Jaeger\Constants;
use OpenTracing\Formats;
use OpenTracing\Reference;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class JaegerMiddleware implements MiddlewareInterface
{
    public const JAEGER_SERVER_SPAN = 'jaeger_server_span';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $serverName;

    /**
     * @var string
     */
    private $agentHost;

    public function __construct(Config $config, string $serverName, string $agentHost)
    {
        $this->config = $config;
        $this->serverName = $serverName;
        $this->agentHost = $agentHost;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $tracer = $this->config->initTracer($this->serverName, $this->agentHost);

        $serverSpan = $tracer->startSpan($request->getRequestTarget(), [
            Reference::CHILD_OF => $tracer->extract(Formats\TEXT_MAP, [
                Constants\Tracer_State_Header_Name => $request->getHeaderLine(Constants\Tracer_State_Header_Name),
            ]),
        ]);

        $request = $request->withAttribute(self::JAEGER_SERVER_SPAN, $serverSpan);

        $response = $handler->handle($request);

        $serverSpan->finish();

        $this->config->flush();

        return $response;
    }
}
