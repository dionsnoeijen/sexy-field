<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;
use Tardigrades\Helper\ArrayConverter;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\FieldConfig
 * @covers ::<private>
 * @covers ::__construct
 */
class FieldConfigTest extends TestCase
{
    /**
     * @test
     * @covers ::fromArray
     * @covers ::toArray
     * @covers ::getName
     * @covers ::getHandle
     * @covers ::getMethodName
     * @covers ::getPropertyName
     */
    public function it_should_create()
    {
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles'
            ]
        ];
        $fConfig = FieldConfig::fromArray($fieldConfig);
        $this->assertInstanceOf(FieldConfig::class, $fConfig);
        $this->assertSame($fieldConfig, $fConfig->toArray());
        $this->assertEquals($fConfig->getHandle(), Handle::fromString('sexy handles'));
        $this->assertEquals($fConfig->getName(), Name::fromString('sexy name'));
        $this->assertEquals($fConfig->getMethodName(), MethodName::fromString('sexy handles'));
        $this->assertEquals($fConfig->getPropertyName(), PropertyName::fromString('sexy handles'));
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getGeneratorConfig
     */
    public function it_should_get_generator_config_if_it_is_defined()
    {
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles',
                'generator' => 'I generate sexiness'
            ]
        ];
        $fConfig = FieldConfig::fromArray($fieldConfig);
        $this->assertInstanceOf(GeneratorConfig::class, $fConfig->getGeneratorConfig());
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getMetadata
     */
    public function it_should_get_metadata_if_it_is_defined()
    {
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles'
                ],
                'metadata' => ['meta-sexyness']
        ];
        $fConfig = FieldConfig::fromArray($fieldConfig);
        $this->assertInstanceOf(FieldMetadata::class, $fConfig->getMetadata());
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getRelationshipKind
     */
    public function it_should_get_kind_if_it_is_defined()
    {
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles',
                'kind' => 'some sexy kind'
            ],
            'metadata' => ['meta-sexyness']
        ];
        $fConfig = FieldConfig::fromArray($fieldConfig);
        $this->assertSame('some sexy kind', $fConfig->getRelationshipKind());
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getRelationshipTo
     */
    public function it_should_get_to_if_it_is_defined()
    {
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles',
                'to' => 'some sexy destination'
            ],
            'metadata' => ['meta-sexyness']
        ];
        $fConfig = FieldConfig::fromArray($fieldConfig);
        $this->assertSame('some sexy destination', $fConfig->getRelationshipTo());
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getEntityEvents
     */
    public function it_should_get_EntityEvents_if_it_is_defined()
    {
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles',
                'entityEvents' => ['some sexy Event', 'more sexy events']
            ]
        ];
        $fConfig = FieldConfig::fromArray($fieldConfig);
        $this->assertSame(['some sexy Event' , 'more sexy events'], $fConfig->getEntityEvents());
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getRelationshipTo
     */
    public function it_should_throw_exceptions_if_to_is_not_well_defined()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Relationship to is empty');
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles',
                'to' => ''
            ]
        ];
        FieldConfig::fromArray($fieldConfig)->getRelationshipTo();

        $this->expectExceptionMessage('No relationship to defined');
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles'
            ]
        ];
        FieldConfig::fromArray($fieldConfig)->getRelationshipTo();

        $this->expectExceptionMessage('Relationship to must be defined as string');
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles',
                'to' => [1,2,3]
            ]
        ];
        FieldConfig::fromArray($fieldConfig)->getRelationshipTo();
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getRelationshipKind
     */
    public function it_should_throw_exceptions_if_kind_is_not_well_defined()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Relationship kind is empty');
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles',
                'kind' => ''
            ]
        ];
        FieldConfig::fromArray($fieldConfig)->getRelationshipKind();

        $this->expectExceptionMessage('No relationship kind defined');
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles'
            ]
        ];
        FieldConfig::fromArray($fieldConfig)->getRelationshipKind();

        $this->expectExceptionMessage('Relationship kind must be defined as string');
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles',
                'kind' => [1,2,3]
            ]
        ];
        FieldConfig::fromArray($fieldConfig)->getRelationshipKind();
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getEntityEvents
     */
    public function it_should_throw_exceptions_if_entityEvents_is_not_well_defined()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Entity events not defined');
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles'
            ]
        ];
        FieldConfig::fromArray($fieldConfig)->getEntityEvents();

        $this->expectExceptionMessage('Entity events should be an array of events you want a generator to run for.');
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles',
                'entityEvents' => 'more sexy events'
            ]
        ];
        FieldConfig::fromArray($fieldConfig)->getEntityEvents();
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $fieldConfig = [
            'field' => [
                'name' => 'sexy name',
                'handle' => 'sexy handles'
            ]
        ];
        $fConfigString = (string)FieldConfig::fromArray($fieldConfig);
        $fieldConfigString = ArrayConverter::recursive($fieldConfig['field']);
        $this->assertSame($fConfigString, $fieldConfigString);
    }
}
