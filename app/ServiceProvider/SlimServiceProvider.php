<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;
use Pimple\ServiceProviderInterface;
use Slim\CallableResolver;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Routing\RouteCollector;

final class SlimServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container[CallableResolver::class] = static function () use ($container) {
            return new CallableResolver($container[PsrContainer::class]);
        };

        $container[RouteCollector::class] = static function () use ($container) {
            return new RouteCollector(
                $container['api-http.response.factory'],
                $container[CallableResolver::class],
                $container[PsrContainer::class],
                new RequestHandler(true),
                null,
                $container['routerCacheFile']
            );
        };

        $container['router'] = static function () use ($container) {
            return $container[RouteCollector::class]->getRouteParser();
        };
    }
}