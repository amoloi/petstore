<?php

declare(strict_types=1);

namespace App\Config;

use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use Monolog\Logger;
use Zend\Expressive\Router\FastRouteRouter;

class DevConfig extends ProdConfig
{
    /**
     * @return array
     */
    public function getConfig(): array
    {
        $config = parent::getConfig();

        $config['cors']['allow-origin'] = [
            '^https?://localhost:3000' => AllowOriginRegex::class,
        ];

        $config['debug'] = true;

        $config['doctrine.dbal.db.options']['configuration']['cache.result']['type'] = 'array';

        $config['doctrine.orm.em.options']['cache.hydration']['type'] = 'array';
        $config['doctrine.orm.em.options']['cache.metadata']['type'] = 'array';
        $config['doctrine.orm.em.options']['cache.query']['type'] = 'array';

        $config['fastroute'][FastRouteRouter::CONFIG_CACHE_ENABLED] = false;
        $config['monolog']['level'] = Logger::DEBUG;

        return $config;
    }

    protected function getEnv(): string
    {
        return 'dev';
    }
}
