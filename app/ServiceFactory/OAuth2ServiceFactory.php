<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\OAuth2\Repository\AccessTokenRepository;
use App\OAuth2\Repository\ClientRepository;
use App\OAuth2\Repository\ScopeRepository;
use Defuse\Crypto\Key;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Psr\Container\ContainerInterface;

final class OAuth2ServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            ClientRepositoryInterface::class => static function (ContainerInterface $container) {
                return new ClientRepository($container->get('doctrine.orm.em'));
            },
            AccessTokenRepositoryInterface::class => static function (ContainerInterface $container) {
                return new AccessTokenRepository($container->get('doctrine.orm.em'));
            },
            ScopeRepositoryInterface::class => static function (ContainerInterface $container) {
                return new ScopeRepository($container->get('doctrine.orm.em'));
            },
            AuthorizationServer::class => static function (ContainerInterface $container) {
                $oauth2 = $container->get('oauth2');

                return new AuthorizationServer(
                    $container->get(ClientRepositoryInterface::class),
                    $container->get(AccessTokenRepositoryInterface::class),
                    $container->get(ScopeRepositoryInterface::class),
                    $oauth2['privateKey'],
                    Key::loadFromAsciiSafeString($oauth2['encryptionKey'])
                );
            },
        ];
    }
}
