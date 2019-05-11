<?php

declare(strict_types=1);

namespace App\Config;

use Zend\Expressive\Router\FastRouteRouter;

class ProdConfig extends AbstractConfig
{
    /**
     * @return array
     */
    public function getConfig(): array
    {
        $cacheDir = $this->getCacheDir();

        return [
            'debug' => false,
            'doctrine.dbal.db.options' => [
                'configuration' => [
                    'cache.result' => ['type' => 'apcu'],
                ],
                'connection' => [
                    'charset' => 'utf8',
                    'dbname' => 'petshop',
                    'driver' => 'pdo_pgsql',
                    'host' => 'localhost',
                    'port' => 5432,
                    'password' => 'root',
                    'user' => 'root',
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
        ];
    }

    /**
     * @return string
     */
    protected function getEnv(): string
    {
        return 'prod';
    }
}
