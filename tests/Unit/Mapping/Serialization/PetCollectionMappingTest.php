<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Collection\PetCollection;
use App\Mapping\Serialization\AbstractCollectionMapping;
use App\Mapping\Serialization\PetCollectionMapping;

/**
 * @covers \App\Mapping\Serialization\PetCollectionMapping
 *
 * @internal
 */
final class PetCollectionMappingTest extends CollectionMappingTest
{
    protected function getClass(): string
    {
        return PetCollection::class;
    }

    protected function getNormalizationType(): string
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

    protected function getCollectionPath(): string
    {
        return '/api/pets';
    }

    protected function getCollectionMapping(): AbstractCollectionMapping
    {
        return new PetCollectionMapping();
    }
}
