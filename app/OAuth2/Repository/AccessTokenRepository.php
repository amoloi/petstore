<?php

declare(strict_types=1);

namespace App\OAuth2\Repository;

use App\OAuth2\Model\AccessToken;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

final class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array<int, ScopeEntityInterface> $scopes
     * @param string|int|null                  $userIdentifier
     */
    public function getNewToken(
        ClientEntityInterface $client,
        array $scopes,
        $userIdentifier = null
    ): AccessTokenEntityInterface {
        $accessToken = new AccessToken();
        $accessToken->setClient($client);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);

        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessToken): void
    {
        $this->entityManager->persist($accessToken);
        $this->entityManager->flush();
    }

    /**
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId): void
    {
        /** @var AccessToken $accessToken */
        $accessToken = $this->entityManager->find(AccessToken::class, $tokenId);
        $accessToken->setRevoked(new \DateTimeImmutable('now'));

        $this->entityManager->persist($accessToken);
        $this->entityManager->flush();
    }

    /**
     * @param string $tokenId
     */
    public function isAccessTokenRevoked($tokenId): bool
    {
        /** @var AccessToken $accessToken */
        $accessToken = $this->entityManager->find(AccessToken::class, $tokenId);

        return null !== $accessToken->getRevoked();
    }
}
