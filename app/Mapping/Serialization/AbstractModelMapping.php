<?php

declare(strict_types=1);

namespace App\Mapping\Serialization;

use App\Model\ModelInterface;
use Chubbyphp\Serialization\Link\LinkBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingInterface;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMapping;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMappingInterface;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;
use Chubbyphp\Serialization\Normalizer\CallbackLinkNormalizer;

abstract class AbstractModelMapping implements NormalizationObjectMappingInterface
{
    /**
     * @return array<NormalizationFieldMappingInterface>
     */
    public function getNormalizationFieldMappings(string $path): array
    {
        return [
            NormalizationFieldMappingBuilder::create('id')->getMapping(),
            NormalizationFieldMappingBuilder::createDateTime('createdAt', \DateTime::ATOM)->getMapping(),
            NormalizationFieldMappingBuilder::createDateTime('updatedAt', \DateTime::ATOM)->getMapping(),
        ];
    }

    /**
     * @return array<NormalizationFieldMappingInterface>
     */
    public function getNormalizationEmbeddedFieldMappings(string $path): array
    {
        return [];
    }

    /**
     * @return array<NormalizationLinkMappingInterface>
     */
    public function getNormalizationLinkMappings(string $path): array
    {
        return [
            new NormalizationLinkMapping('read', [], new CallbackLinkNormalizer(
                function (string $path, ModelInterface $model) {
                    return LinkBuilder::create(sprintf($this->getReadPath(), $model->getId()))
                        ->setAttributes(['method' => 'GET'])
                        ->getLink()
                    ;
                }
            )),
            new NormalizationLinkMapping('update', [], new CallbackLinkNormalizer(
                function (string $path, ModelInterface $model) {
                    return LinkBuilder::create(sprintf($this->getUpdatePath(), $model->getId()))
                        ->setAttributes(['method' => 'PUT'])
                        ->getLink()
                    ;
                }
            )),
            new NormalizationLinkMapping('delete', [], new CallbackLinkNormalizer(
                function (string $path, ModelInterface $model) {
                    return LinkBuilder::create(sprintf($this->getDeletePath(), $model->getId()))
                        ->setAttributes(['method' => 'DELETE'])
                        ->getLink()
                    ;
                }
            )),
        ];
    }

    abstract protected function getReadPath(): string;

    abstract protected function getUpdatePath(): string;

    abstract protected function getDeletePath(): string;
}
