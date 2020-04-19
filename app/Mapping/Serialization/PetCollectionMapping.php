<?php

declare(strict_types=1);

namespace App\Mapping\Serialization;

use App\Collection\PetCollection;

final class PetCollectionMapping extends AbstractCollectionMapping
{
    public function getClass(): string
    {
        return PetCollection::class;
    }

    public function getNormalizationType(): string
    {
        return 'petCollection';
    }

    protected function getListPath(): string
    {
        return '/api/pets';
    }

    protected function getCreatePath(): string
    {
        return '/api/pets';
    }
}
