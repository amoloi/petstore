<?php

declare(strict_types=1);

namespace App\Mapping\Serialization;

use App\Model\Pet;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingInterface;

final class PetMapping extends AbstractModelMapping
{
    public function getClass(): string
    {
        return Pet::class;
    }

    public function getNormalizationType(): string
    {
        return 'pet';
    }

    /**
     * @return array<NormalizationFieldMappingInterface>
     */
    public function getNormalizationFieldMappings(string $path): array
    {
        $normalizationFieldMappings = parent::getNormalizationFieldMappings($path);
        $normalizationFieldMappings[] = NormalizationFieldMappingBuilder::create('name')->getMapping();
        $normalizationFieldMappings[] = NormalizationFieldMappingBuilder::create('tag')->getMapping();
        $normalizationFieldMappings[] = NormalizationFieldMappingBuilder::createEmbedMany('vaccinations')->getMapping();

        return $normalizationFieldMappings;
    }
}
