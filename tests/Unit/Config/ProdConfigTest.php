<?php

declare(strict_types=1);

namespace App\Tests\Unit\Config;

use App\Config\ProdConfig;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Config\AbstractConfig
 * @covers \App\Config\ProdConfig
 *
 * @internal
 */
final class ProdConfigTest extends TestCase
{
    public function testGetConfig(): void
    {
        $config = ProdConfig::create('/path/to/root');

        self::assertSame([
            'debug' => false,
            'doctrine.dbal.db.options' => [
                'configuration' => [
                    'cache.result' => [
                        'type' => 'apcu',
                    ],
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
                'cache.hydration' => [
                    'type' => 'apcu',
                ],
                'cache.metadata' => [
                    'type' => 'apcu',
                ],
                'cache.query' => [
                    'type' => 'apcu',
                ],
                'proxies.dir' => '/path/to/root/var/cache/prod/doctrine/proxies',
            ],
            'fastroute' => [
                'cache_file' => '/path/to/root/var/cache/prod/routes.php',
                'cache_enabled' => true,
            ],
            'monolog' => [
                'name' => 'petstore',
                'path' => '/path/to/root/var/log/prod/application.log',
                'level' => 250,
            ],
        ], $config->getConfig());
    }

    public function testGetDirectories(): void
    {
        $config = ProdConfig::create('/path/to/root');

        self::assertSame([
            'cache' => '/path/to/root/var/cache/prod',
            'log' => '/path/to/root/var/log/prod',
        ], $config->getDirectories());
    }
}
