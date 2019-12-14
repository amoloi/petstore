<?php

declare(strict_types=1);

namespace App\Config;

use Monolog\Logger;
use Zend\Expressive\Router\FastRouteRouter;

class ProdConfig extends AbstractConfig
{
    public function getConfig(): array
    {
        $cacheDir = $this->getCacheDir();
        $logDir = $this->getLogDir();

        return [
            'cors' => [
                'allow-origin' => [],
                'allow-methods' => ['DELETE', 'GET', 'POST', 'PUT'],
                'allow-headers' => [
                    'Accept',
                    'Content-Type',
                ],
                'allow-credentials' => false,
                'expose-headers' => [],
                'max-age' => 7200,
            ],
            'debug' => false,
            'doctrine.dbal.db.options' => [
                'configuration' => [
                    'cache.result' => ['type' => 'apcu'],
                ],
                'connection' => [
                    'driver' => 'pdo_pgsql',
                    'charset' => 'utf8',
                    'user' => getenv('DATABASE_USER'),
                    'password' => getenv('DATABASE_PASS'),
                    'host' => getenv('DATABASE_HOST'),
                    'port' => getenv('DATABASE_PORT'),
                    'dbname' => getenv('DATABASE_NAME'),
                ],
            ],
            'doctrine.orm.em.options' => [
                'cache.hydration' => ['type' => 'apcu'],
                'cache.metadata' => ['type' => 'apcu'],
                'cache.query' => ['type' => 'apcu'],
                'proxies.dir' => $cacheDir.'/doctrine/proxies',
            ],
            'fastroute' => [
                FastRouteRouter::CONFIG_CACHE_FILE => $cacheDir.'/routes.php',
                FastRouteRouter::CONFIG_CACHE_ENABLED => true,
            ],
            'monolog' => [
                'name' => 'petstore',
                'path' => $logDir.'/application.log',
                'level' => Logger::NOTICE,
            ],
        ];
    }

    public function getEnv(): string
    {
        return 'prod';
    }
}
