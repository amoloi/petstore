<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Mapping\Serialization\AbstractModelMapping;
use App\Model\ModelInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Serialization\AbstractModelMapping
 *
 * @internal
 */
class ModelMappingTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetClass(): void
    {
        $mapping = $this->getModelMapping();

        self::assertSame($this->getClass(), $mapping->getClass());
    }

    public function testGetNormalizationType(): void
    {
        $mapping = $this->getModelMapping();

        self::assertSame($this->getNormalizationType(), $mapping->getNormalizationType());
    }

    public function testGetNormalizationFieldMappings(): void
    {
        $mapping = $this->getModelMapping();

        $fieldMappings = $mapping->getNormalizationFieldMappings('/');

        self::assertEquals([
            NormalizationFieldMappingBuilder::create('id')->getMapping(),
            NormalizationFieldMappingBuilder::createDateTime('createdAt', \DateTime::ATOM)->getMapping(),
            NormalizationFieldMappingBuilder::createDateTime('updatedAt', \DateTime::ATOM)->getMapping(),
        ], $fieldMappings);
    }

    public function testGetNormalizationEmbeddedFieldMappings(): void
    {
        $mapping = $this->getModelMapping();

        $fieldMappings = $mapping->getNormalizationEmbeddedFieldMappings('/');

        self::assertEquals([], $fieldMappings);
    }

    public function testGetNormalizationLinkMappings(): void
    {
        $mapping = $this->getModelMapping();

        $linkMappings = $mapping->getNormalizationLinkMappings('/');

        self::assertEquals([], $linkMappings);
    }

    protected function getClass(): string
    {
        return ModelInterface::class;
    }

    protected function getNormalizationType(): string
    {
        return 'model';
    }

    protected function getModelMapping(): AbstractModelMapping
    {
        return new class($this->getClass(), $this->getNormalizationType()) extends AbstractModelMapping {
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
