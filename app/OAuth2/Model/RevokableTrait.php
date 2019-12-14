<?php

declare(strict_types=1);

namespace App\OAuth2\Model;

trait RevokableTrait
{
    /**
     * @var \DateTimeImmutable|null
     */
    private $revoked;

    public function setRevoked(?\DateTimeImmutable $revoked): void
    {
        $this->revoked = $revoked;
    }

    public function getRevoked(): ?\DateTimeImmutable
    {
        return $this->revoked;
    }
}
