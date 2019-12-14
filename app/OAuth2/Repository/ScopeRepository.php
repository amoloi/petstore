<?php

declare(strict_types=1);

namespace App\OAuth2\Repository;

use App\OAuth2\Model\Scope;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

final class ScopeRepository implements ScopeRepositoryInterface
{
    /**
     * @param string $identifier
     */
    public function getScopeEntityByIdentifier($identifier): ?ScopeEntityInterface
    {
        /** @var Scope $scope */
        return $this->entityManager->find(Scope::class, $identifier);
    }

    /**
     * @param array<int, ScopeEntityInterface> $scopes
     * @param string                           $grantType
     * @param string|null                      $userIdentifier
     *
     * @return array<int, ScopeEntityInterface>
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ): array {
        return $scopes;
    }
}
