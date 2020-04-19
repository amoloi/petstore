<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Collection\AbstractCollection;
use App\Collection\CollectionInterface;
use App\Mapping\Serialization\AbstractCollectionMapping;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMappingInterface;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\Mapping\Serialization\AbstractCollectionMapping
 *
 * @internal
 */
class CollectionMappingTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetClass(): void
    {
        $mapping = $this->getCollectionMapping();

        self::assertSame($this->getClass(), $mapping->getClass());
    }

    public function testGetNormalizationType(): void
    {
        $mapping = $this->getCollectionMapping();

        self::assertSame($this->getNormalizationType(), $mapping->getNormalizationType());
    }

    public function testGetNormalizationFieldMappings(): void
    {
        $mapping = $this->getCollectionMapping();

        $fieldMappings = $mapping->getNormalizationFieldMappings('/');

        self::assertEquals($this->getNormalizationFieldMappings('/'), $fieldMappings);
    }

    public function testGetNormalizationEmbeddedFieldMappings(): void
    {
        $mapping = $this->getCollectionMapping();

        $fieldMappings = $mapping->getNormalizationEmbeddedFieldMappings('/');

        self::assertEquals([
            NormalizationFieldMappingBuilder::createEmbedMany('items')->getMapping(),
        ], $fieldMappings);
    }

    public function testGetNormalizationLinkMappings(): void
    {
        $mapping = $this->getCollectionMapping();

        $linkMappings = $mapping->getNormalizationLinkMappings('/');

        self::assertCount(2, $linkMappings);

        self::assertInstanceOf(NormalizationLinkMappingInterface::class, $linkMappings[0]);
        self::assertInstanceOf(NormalizationLinkMappingInterface::class, $linkMappings[1]);

        $object = new class() extends AbstractCollection {
        };

        $object->setOffset(0);
        $object->setLimit(20);
        $object->setCount(25);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getQueryParams')->with()->willReturn(['key' => 'value']),
        ]);

        /** @var NormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(NormalizerContextInterface::class, [
            Call::create('getRequest')->with()->willReturn($request),
        ]);

        $list = $linkMappings[0]->getLinkNormalizer()->normalizeLink('/', $object, $context);
        $create = $linkMappings[1]->getLinkNormalizer()->normalizeLink('/', $object, $context);

        self::assertSame([
            'href' => sprintf('%s?key=value&offset=0&limit=20', $this->getCollectionPath()),
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'GET',
            ],
        ], $list);

        self::assertSame([
            'href' => sprintf('%s', $this->getCollectionPath()),
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'POST',
            ],
        ], $create);
    }

    /**
     * @return NormalizationFieldMappingInterface[]
     */
    protected function getNormalizationFieldMappings(string $path): array
    {
        return [
            NormalizationFieldMappingBuilder::create('offset')->getMapping(),
            NormalizationFieldMappingBuilder::create('limit')->getMapping(),
            NormalizationFieldMappingBuilder::create('count')->getMapping(),
            NormalizationFieldMappingBuilder::create('filters')->getMapping(),
            NormalizationFieldMappingBuilder::create('sort')->getMapping(),
        ];
    }

    protected function getClass(): string
    {
        return CollectionInterface::class;
    }

    protected function getNormalizationType(): string
    {
        return 'collection';
    }

    protected function getListPath(): string
    {
        return '/api/collection';
    }

    protected function getCreatePath(): string
    {
        return '/api/collection';
    }

    protected function getCollectionPath(): string
    {
        return '/api/collection';
    }

    protected function getCollectionMapping(): AbstractCollectionMapping
    {
        return new class($this->getClass(), $this->getNormalizationType(), $this->getListPath(), $this->getCreatePath()) extends AbstractCollectionMapping {
            /**
             * @var string
             */
            private $class;

            /**
             * @var string
             */
            private $normalizationType;

            /**
             * @var string
             */
            private $listPath;

            /**
             * @var string
             */
            private $createPath;

            public function __construct(
                string $class,
                string $normalizationType,
                string $listPath,
                string $createPath
            ) {
                $this->class = $class;
                $this->normalizationType = $normalizationType;
                $this->listPath = $listPath;
                $this->createPath = $createPath;
            }

            public function getClass(): string
            {
                return $this->class;
            }

            public function getNormalizationType(): string
            {
                return $this->normalizationType;
            }

            protected function getListPath(): string
            {
                return $this->listPath;
            }

            protected function getCreatePath(): string
            {
                return $this->createPath;
            }
        };
    }
}
