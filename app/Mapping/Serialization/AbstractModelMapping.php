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
use Zend\Expressive\Router\RouterInterface;

abstract class AbstractModelMapping implements NormalizationObjectMappingInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

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
                    return LinkBuilder
                        ::create(
                            $this->router->generateUri($this->getReadRouteName(), ['id' => $model->getId()])
                        )
                            ->setAttributes(['method' => 'GET'])
                            ->getLink()
                    ;
                }
            )),
            new NormalizationLinkMapping('update', [], new CallbackLinkNormalizer(
                function (string $path, ModelInterface $model) {
                    return LinkBuilder
                        ::create(
                            $this->router->generateUri($this->getUpdateRouteName(), ['id' => $model->getId()])
                        )
                            ->setAttributes(['method' => 'PUT'])
                            ->getLink()
                    ;
                }
            )),
            new NormalizationLinkMapping('delete', [], new CallbackLinkNormalizer(
                function (string $path, ModelInterface $model) {
                    return LinkBuilder
                        ::create(
                            $this->router->generateUri($this->getDeleteRouteName(), ['id' => $model->getId()])
                        )
                            ->setAttributes(['method' => 'DELETE'])
                            ->getLink()
                    ;
                }
            )),
        ];
    }

    abstract protected function getReadRouteName(): string;

    abstract protected function getUpdateRouteName(): string;

    abstract protected function getDeleteRouteName(): string;
}
