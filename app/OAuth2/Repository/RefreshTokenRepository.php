<?php

declare(strict_types=1);

namespace App\OAuth2\Repository;

use App\OAuth2\Model\RefreshToken;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

final class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getNewRefreshToken(): ?RefreshTokenEntityInterface
    {
        return new RefreshToken();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshToken): void
    {
        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();
    }

    /**
     * @param string $tokenId
     */
    public function revokeRefreshToken($tokenId): void
    {
        /** @var RefreshToken $refreshToken */
        $refreshToken = $this->entityManager->find(RefreshToken::class, $tokenId);
        $refreshToken->setRevoked(new \DateTimeImmutable('now'));

        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();
    }

    /**
     * @param string $tokenId
     */
    public function isRefreshTokenRevoked($tokenId): bool
    {
        /** @var RefreshToken $refreshToken */
        $refreshToken = $this->entityManager->find(RefreshToken::class, $tokenId);

        return null !== $refreshToken->getRevoked();
    }
}
