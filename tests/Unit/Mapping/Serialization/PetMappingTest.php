<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Mapping\Serialization\AbstractModelMapping;
use App\Mapping\Serialization\PetMapping;
use App\Model\Pet;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;

/**
 * @covers \App\Mapping\Serialization\PetMapping
 *
 * @internal
 */
final class PetMappingTest extends ModelMappingTest
{
    use MockByCallsTrait;

    public function testGetNormalizationFieldMappings(): void
    {
        $mapping = $this->getModelMapping();

        $fieldMappings = $mapping->getNormalizationFieldMappings('/');

        self::assertEquals([
            NormalizationFieldMappingBuilder::create('id')->getMapping(),
            NormalizationFieldMappingBuilder::createDateTime('createdAt', \DateTime::ATOM)->getMapping(),
            NormalizationFieldMappingBuilder::createDateTime('updatedAt', \DateTime::ATOM)->getMapping(),
            NormalizationFieldMappingBuilder::create('name')->getMapping(),
            NormalizationFieldMappingBuilder::create('tag')->getMapping(),
            NormalizationFieldMappingBuilder::createEmbedMany('vaccinations')->getMapping(),
        ], $fieldMappings);
    }

    protected function getClass(): string
    {
        return Pet::class;
    }

    protected function getNormalizationType(): string
    {
        return 'pet';
    }

    protected function getReadPath(): string
    {
        return '/api/pets/%s';
    }

    protected function getUpdatePath(): string
    {
        return '/api/pets/%s';
    }

    protected function getDeletePath(): string
    {
        return '/api/pets/%s';
    }

    protected function getModelPath(): string
    {
        return '/api/pets/%s';
    }

    protected function getModelMapping(): AbstractModelMapping
    {
        return new PetMapping();
    }
}
