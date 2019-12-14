<?php

declare(strict_types=1);

namespace App\OAuth2\Repository;

use App\OAuth2\Model\AuthCode;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

final class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        return new AuthCode();
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCode): void
    {
        $this->entityManager->persist($authCode);
        $this->entityManager->flush();
    }

    /**
     * @param string $codeId
     */
    public function revokeAuthCode($codeId): void
    {
        /** @var AuthCode $authCode */
        $authCode = $this->entityManager->find(AuthCode::class, $codeId);
        $authCode->setRevoked(new \DateTimeImmutable('now'));

        $this->entityManager->persist($authCode);
        $this->entityManager->flush();
    }

    /**
     * @param string $codeId
     */
    public function isAuthCodeRevoked($codeId): bool
    {
        /** @var AuthCode $authCode */
        $authCode = $this->entityManager->find(AuthCode::class, $codeId);

        return null !== $authCode->getRevoked();
    }
}
