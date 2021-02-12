<?php
declare (strict_types = 1);

namespace Tardigrades\FieldType;

use Mockery;
use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\FieldTypeGeneratorConfig;

/**
 * @coversDefaultClass Tardigrades\FieldType\FieldType
 * @covers ::<private>
 * @covers ::__construct
 * @covers ::setConfig
 */
final class FieldTypeTest extends TestCase
{

    /**
     * @test
     * @covers ::getConfig
     * @covers ::getFieldTypeGeneratorConfig
     * @covers ::formOptions
     */
    public function it_should_create()
    {
        $fieldType = $this->givenAFieldType();

        $sectionEntity = Mockery::mock("Foo");
        $sectionEntity->shouldReceive('getId')
            ->andReturn(null);

        $this->assertEquals(FieldTypeGeneratorConfig::fromArray([]), $fieldType->getFieldTypeGeneratorConfig());
        $this->assertEquals($this->givenAFieldConfig(), $fieldType->getConfig());
        $this->assertEquals(['foo' => 'bar', 'a' => 'b'], $fieldType->formOptions($sectionEntity));
    }

    /**
     * @test
     * @covers ::getConfig
     * @covers ::getFieldTypeGeneratorConfig
     * @covers ::formOptions
     */
    public function it_should_update()
    {
        $fieldType = $this->givenAFieldType();

        $sectionEntity = Mockery::mock("Foo");
        $sectionEntity->shouldReceive('getId')
            ->andReturn(8);

        $this->assertEquals(FieldTypeGeneratorConfig::fromArray([]), $fieldType->getFieldTypeGeneratorConfig());
        $this->assertEquals($this->givenAFieldConfig(), $fieldType->getConfig());
        $this->assertEquals(['foo' => 'bar', 'one' => 'two'], $fieldType->formOptions($sectionEntity));
    }

    /**
     * @test
     * @covers ::hasEntityEvent
     */
    public function it_should_check_entity_events()
    {
        $fieldType = $this->givenAFieldType();
        $this->assertTrue($fieldType->hasEntityEvent('beta'));
        $this->assertFalse($fieldType->hasEntityEvent('gamma'));
        $otherFieldType = $this->givenAFieldTypeWithoutGeneratorEntityEvents();
        $this->assertFalse($otherFieldType->hasEntityEvent('delta'));
    }

//    /**
//     * @test
//     * @covers ::directory
//     */
//    public function it_should_return_its_directory()
//    {
//        $fieldType = $this->givenAFieldType();
//        $this->assertMatchesRegularExpression('/phpunit-mock-objects/', $fieldType->directory());
//    }

    private function givenAFieldType()
    {
        return $this
            ->getMockForAbstractClass(FieldType::class)
            ->setConfig(
                $this->givenAFieldConfig()
            );
    }

    private function givenAFieldConfig()
    {
        return FieldConfig::fromArray(
            [
                'field' => [
                    'name' => 'sexyname',
                    'handle' => 'lovehandles',
                    'form' => [
                        'all' => ['foo' => 'bar'],
                        'create' => ['a' => 'b'],
                        'update' => ['one' => 'two']
                    ],
                    'generator' => [
                        'entity' => [
                            'event' => ['alpha', 'beta']
                        ]
                    ]
                ]
            ]
        );
    }

    private function givenAFieldTypeWithoutGeneratorEntityEvents()
    {
        return $this->getMockForAbstractClass(FieldType::class)
            ->setConfig($this->givenAFieldConfigWithoutGeneratorEntityEvents());
    }

    private function givenAFieldConfigWithoutGeneratorEntityEvents()
    {
        return FieldConfig::fromArray(
            [
                'field' => [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'form' => [
                            'all' => ['foo' => 'bar'],
                            'create' => ['a' => 'b'],
                            'update' => ['one' => 'two']
                        ],
                        'generator' => []
                ]
            ]
        );
    }
}
