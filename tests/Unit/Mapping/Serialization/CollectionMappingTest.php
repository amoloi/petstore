<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Collection\CollectionInterface;
use App\Mapping\Serialization\AbstractCollectionMapping;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use PHPUnit\Framework\TestCase;

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

        self::assertEquals([], $linkMappings);
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

    protected function getCollectionMapping(): AbstractCollectionMapping
    {
        return new class($this->getClass(), $this->getNormalizationType()) extends AbstractCollectionMapping {
            /**
             * @var string
             */
            private $class;

            /**
             * @var string
             */
            private $normalizationType;

            public function __construct(
                string $class,
                string $normalizationType
            ) {
                $this->class = $class;
                $this->normalizationType = $normalizationType;
            }

            public function getClass(): string
            {
                return $this->class;
            }

            public function getNormalizationType(): string
            {
                return $this->normalizationType;
            }
        };
    }
}
