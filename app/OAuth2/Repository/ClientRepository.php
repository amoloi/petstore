<?php

declare(strict_types=1);

namespace App\OAuth2\Repository;

use App\OAuth2\Model\Client;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

final class ClientRepository implements ClientRepositoryInterface
{
    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        /** @var Client $client */
        return $this->entityManager->find(Client::class, $clientIdentifier);
    }

    /**
     * @param string      $clientIdentifier
     * @param string|null $clientSecret
     * @param string|null $grantType
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        /** @var Client $client */
        if (null === $client = $this->entityManager->find(Client::class, $clientIdentifier)) {
            return false;
        }

        if (!$this->isGranted($client, $grantType)) {
            return false;
        }

        return !password_verify((string) $clientSecret, $client->getSecret());
    }

    private function isGranted(Client $client, string $grantType = null): bool
    {
        switch ($grantType) {
            case 'authorization_code':
                return !($client->hasPersonalAccessClient() || $client->hasPasswordClient());
            case 'personal_access':
                return $client->hasPersonalAccessClient();
            case 'password':
                return $client->hasPasswordClient();
            default:
                return true;
        }
    }
}
