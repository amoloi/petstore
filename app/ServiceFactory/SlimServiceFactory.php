<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use Psr\Container\ContainerInterface;
use Slim\CallableResolver;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteCollector;

final class SlimServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            CallableResolverInterface::class => static function (ContainerInterface $container) {
                return new CallableResolver($container);
            },
            RouteCollectorInterface::class => static function (ContainerInterface $container) {
                return new RouteCollector(
                    $container->get('api-http.response.factory'),
                    $container->get(CallableResolverInterface::class),
                    $container,
                    new RequestHandler(true),
                    null,
                    $container->get('routerCacheFile')
                );
            },
            RouteParserInterface::class => static function (ContainerInterface $container) {
                return $container->get(RouteCollectorInterface::class)->getRouteParser();
            },
        ];
    }
}
