<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Mapping\Serialization\AbstractModelMapping;
use App\Model\ModelInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMappingInterface;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use PHPUnit\Framework\MockObject\MockObject;
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

        self::assertCount(3, $linkMappings);

        self::assertInstanceOf(NormalizationLinkMappingInterface::class, $linkMappings[0]);
        self::assertInstanceOf(NormalizationLinkMappingInterface::class, $linkMappings[1]);
        self::assertInstanceOf(NormalizationLinkMappingInterface::class, $linkMappings[2]);

        /** @var ModelInterface|MockObject $model */
        $model = $this->getMockByCalls(ModelInterface::class, [
            Call::create('getId')->with()->willReturn('f183c7ff-7683-451e-807c-b916d9b5cf86'),
            Call::create('getId')->with()->willReturn('f183c7ff-7683-451e-807c-b916d9b5cf86'),
            Call::create('getId')->with()->willReturn('f183c7ff-7683-451e-807c-b916d9b5cf86'),
        ]);

        /** @var NormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(NormalizerContextInterface::class);

        $read = $linkMappings[0]->getLinkNormalizer()->normalizeLink('/', $model, $context);
        $update = $linkMappings[1]->getLinkNormalizer()->normalizeLink('/', $model, $context);
        $delete = $linkMappings[2]->getLinkNormalizer()->normalizeLink('/', $model, $context);

        self::assertSame([
            'href' => sprintf($this->getModelPath(), 'f183c7ff-7683-451e-807c-b916d9b5cf86'),
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'GET',
            ],
        ], $read);

        self::assertSame([
            'href' => sprintf($this->getModelPath(), 'f183c7ff-7683-451e-807c-b916d9b5cf86'),
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'PUT',
            ],
        ], $update);

        self::assertSame([
            'href' => sprintf($this->getModelPath(), 'f183c7ff-7683-451e-807c-b916d9b5cf86'),
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'DELETE',
            ],
        ], $delete);
    }

    protected function getClass(): string
    {
        return ModelInterface::class;
    }

    protected function getNormalizationType(): string
    {
        return 'model';
    }

    protected function getReadPath(): string
    {
        return '/api/collection/%s';
    }

    protected function getUpdatePath(): string
    {
        return '/api/collection/%s';
    }

    protected function getDeletePath(): string
    {
        return '/api/collection/%s';
    }

    protected function getModelPath(): string
    {
        return '/api/collection/%s';
    }

    protected function getModelMapping(): AbstractModelMapping
    {
        return new class($this->getClass(), $this->getNormalizationType(), $this->getReadPath(), $this->getUpdatePath(), $this->getDeletePath()) extends AbstractModelMapping {
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
            private $readPath;

            /**
             * @var string
             */
            private $updatePath;

            /**
             * @var string
             */
            private $deletePath;

            public function __construct(
                string $class,
                string $normalizationType,
                string $readPath,
                string $updatePath,
                string $deletePath
            ) {
                $this->class = $class;
                $this->normalizationType = $normalizationType;
                $this->readPath = $readPath;
                $this->updatePath = $updatePath;
                $this->deletePath = $deletePath;
            }

            public function getClass(): string
            {
                return $this->class;
            }

            public function getNormalizationType(): string
            {
                return $this->normalizationType;
            }

            protected function getReadPath(): string
            {
                return $this->readPath;
            }

            protected function getUpdatePath(): string
            {
                return $this->updatePath;
            }

            protected function getDeletePath(): string
            {
                return $this->deletePath;
            }
        };
    }
}
