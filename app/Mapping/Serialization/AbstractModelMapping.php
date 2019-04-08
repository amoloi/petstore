<?php

declare(strict_types=1);

namespace App\Mapping\Serialization;

use App\Model\ModelInterface;
use Chubbyphp\Serialization\Link\LinkBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingInterface;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMapping;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;
use Chubbyphp\Serialization\Normalizer\CallbackLinkNormalizer;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;

abstract class AbstractModelMapping implements NormalizationObjectMappingInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param string $path
     *
     * @return NormalizationFieldMappingInterface[]
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
     * @param string $path
     *
     * @return NormalizationFieldMappingInterface[]
     */
    public function getNormalizationEmbeddedFieldMappings(string $path): array
    {
        return [];
    }

    /**
     * @param string $path
     *
     * @return NormalizationLinkMappingInterface[]
     */
    public function getNormalizationLinkMappings(string $path): array
    {
        return [
            new NormalizationLinkMapping('read', [], new CallbackLinkNormalizer(
                function (string $path, ModelInterface $model) {
                    return LinkBuilder
                        ::create(
                            $this->urlGenerator->generatePath($this->getReadRouteName(), ['id' => $model->getId()])
                        )
                        ->setAttributes(['method' => 'GET'])
                        ->getLink();
                }
            )),
            new NormalizationLinkMapping('update', [], new CallbackLinkNormalizer(
                function (string $path, ModelInterface $model) {
                    return LinkBuilder
                        ::create(
                            $this->urlGenerator->generatePath($this->getUpdateRouteName(), ['id' => $model->getId()])
                        )
                        ->setAttributes(['method' => 'PUT'])
                        ->getLink();
                }
            )),
            new NormalizationLinkMapping('delete', [], new CallbackLinkNormalizer(
                function (string $path, ModelInterface $model) {
                    return LinkBuilder
                        ::create(
                            $this->urlGenerator->generatePath($this->getDeleteRouteName(), ['id' => $model->getId()])
                        )
                        ->setAttributes(['method' => 'DELETE'])
                        ->getLink();
                }
            )),
        ];
    }

    /**
     * @return string
     */
    abstract protected function getReadRouteName(): string;

    /**
     * @return string
     */
    abstract protected function getUpdateRouteName(): string;

    /**
     * @return string
     */
    abstract protected function getDeleteRouteName(): string;
}
