<?php

declare(strict_types=1);

namespace App\Config;

use Zend\Expressive\Router\FastRouteRouter;

class DevConfig extends ProdConfig
{
    /**
     * @return array
     */
    public function getConfig(): array
    {
        $config = parent::getConfig();

        $config['debug'] = true;

        $config['doctrine.dbal.db.options']['configuration']['cache.result']['type'] = 'array';

        $config['doctrine.orm.em.options']['cache.hydration']['type'] = 'array';
        $config['doctrine.orm.em.options']['cache.metadata']['type'] = 'array';
        $config['doctrine.orm.em.options']['cache.query']['type'] = 'array';

        $config['fastroute'][FastRouteRouter::CONFIG_CACHE_ENABLED] = false;

        return $config;
    }

    /**
     * @return string
     */
    protected function getEnv(): string
    {
        return 'dev';
    }
}
