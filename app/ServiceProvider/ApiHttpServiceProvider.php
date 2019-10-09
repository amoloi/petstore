<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\ApiHttp\Factory\InvalidParametersFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\StreamFactory;

final class ApiHttpServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container['api-http.response.factory'] = static function () {
            return new ResponseFactory();
        };

        $container['api-http.stream.factory'] = static function () {
            return new StreamFactory();
        };

        $container[InvalidParametersFactory::class] = static function () {
            return new InvalidParametersFactory();
        };
    }
}
