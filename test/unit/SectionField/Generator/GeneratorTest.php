<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Tardigrades\Entity\Field;
use Tardigrades\Entity\FieldType;
use Tardigrades\FieldType\FieldTypeInterface;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\Generator\Writer\Writable;
use Tardigrades\SectionField\Service\FieldManagerInterface;
use Tardigrades\SectionField\Service\FieldTypeManagerInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\FieldTypeGeneratorConfig;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\SectionConfig;

/**
 * @coversDefaultClass Tardigrades\SectionField\Generator\Generator
 * @covers ::__construct
 * @covers ::<protected>
 */
final class GeneratorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var GeneratorStub */
    private $fixture;

    /** @var  FieldManagerInterface|Mockery\MockInterface */
    private $fieldManager;

    /** @var  FieldTypeManagerInterface|Mockery\MockInterface */
    private $fieldTypeManager;

    /** @var  SectionManagerInterface|Mockery\MockInterface */
    private $sectionmanager;

    /** @var  Container|Mockery\MockInterface */
    private $container;


    public function setUp()
    {
        $this->fieldManager = Mockery::mock(FieldManagerInterface::class);
        $this->fieldTypeManager = Mockery::mock(FieldTypeManagerInterface::class);
        $this->sectionmanager = Mockery::mock(SectionManagerInterface::class);
        $this->container = Mockery::mock(Container::class);
        $this->fixture = new GeneratorStub(
            $this->fieldManager,
            $this->fieldTypeManager,
            $this->sectionmanager,
            $this->container
        );
    }

    /**
     * @test
     */
    public function it_should_add_opposing_relationships()
    {
        $sectionOne = $this->givenASectionWithName('One');
        $field = $this->givenAFieldWithNameKindAndTo('Two', 'many-to-one', 'One');

        $relationships = $this->givenRelationshipsOfAll();
        $this->sectionmanager->shouldReceive('getRelationshipsOfAll')->once()->andReturn($relationships);
        $this->fieldTypeManager
            ->shouldReceive('readByFullyQualifiedClassName')
            ->once()
            ->andReturn($field->getFieldType());

        $result = $this->fixture->newAddOpposingRelationships($sectionOne, []);
        $this->assertEquals([$field], $result);
    }

    /**
     * @test
     */
    public function it_should_get_field_type_generator_config()
    {
        $fieldOne = $this->givenAFieldWithNameKindAndTo('One', 'many-to-one', 'Two');
        $fieldType = Mockery::mock(FieldTypeInterface::class);
        $fieldTypeGeneratorConfig = FieldTypeGeneratorConfig::fromArray(['toGenerateFor' => 'something']);
        $fieldType->shouldReceive('getFieldTypeGeneratorConfig')->once()->andReturn($fieldTypeGeneratorConfig);
        $this->container->shouldReceive('get')->once()->andReturn($fieldType);

        $result = $this->fixture->newGetFieldTypeGeneratorConfig($fieldOne, 'toGenerateFor');

        $this->assertSame(['toGenerateFor' => 'something'], $result);
    }

    /**
     * @test
     * @covers ::getBuildMessages
     */
    public function it_should_set_messages_when_field_type_generator_config_is_empty()
    {
        $fieldOne = $this->givenAFieldWithNameKindAndTo('One', 'many-to-one', 'Two');
        $fieldType = Mockery::mock(FieldTypeInterface::class);
        $fieldTypeGeneratorConfig = FieldTypeGeneratorConfig::fromArray([]);
        $fieldType->shouldReceive('getFieldTypeGeneratorConfig')->once()->andReturn($fieldTypeGeneratorConfig);
        $this->container->shouldReceive('get')->once()->andReturn($fieldType);

        $result = $this->fixture->newGetFieldTypeGeneratorConfig($fieldOne, 'toGenerateFor');

        $this->assertSame([], $result);
        $this->assertSame(['No generator defined for fieldOne type: string'], $this->fixture->getBuildMessages());
    }

    /**
     * @test
     * @covers ::getBuildMessages
     */
    public function it_should_set_messages_when_field_type_generator_config_key_is_absent()
    {
        $fieldOne = $this->givenAFieldWithNameKindAndTo('One', 'many-to-one', 'Two');
        $fieldType = Mockery::mock(FieldTypeInterface::class);
        $fieldTypeGeneratorConfig = FieldTypeGeneratorConfig::fromArray(['otherKey' => 'otherValue']);
        $fieldType->shouldReceive('getFieldTypeGeneratorConfig')->once()->andReturn($fieldTypeGeneratorConfig);
        $this->container->shouldReceive('get')->once()->andReturn($fieldType);

        $result = $this->fixture->newGetFieldTypeGeneratorConfig($fieldOne, 'toGenerateFor');

        $this->assertSame(['otherKey' => 'otherValue'], $result);
        $this->assertSame(['Nothing to do for this generator: toGenerateFor'], $this->fixture->getBuildMessages());
    }

    /**
     * @test
     */
    public function it_should_get_field_template_directory()
    {
        $fieldOne = $this->givenAFieldWithNameKindAndTo('One', 'many-to-one', 'Two');
        $fieldType = Mockery::mock(FieldTypeInterface::class);
        $fieldType->shouldReceive('directory')->once()->andReturn('vendor/tardigrades/sexy-field-doctrine');
        $this->container->shouldReceive('get')->once()->andReturn($fieldType);

        $result = $this->fixture->newGetFieldTypeTemplateDirectory($fieldOne, 'sexy-field-doctrine');

        $this->assertSame('vendor/tardigrades/sexy-field-doctrine', $result);
    }

    private function givenASectionWithName($name)
    {
        $sectionName = 'Section ' . $name;
        $sectionHandle = 'section' . $name;

        $sectionConfig = SectionConfig::fromArray([
            'section' => [
                'name' => $sectionName,
                'handle' => $sectionHandle,
                'fields' => [
                    'title',
                    'body',
                    'created'
                ],
                'slug' => ['title'],
                'default' => 'title',
                'namespace' => 'My\\Namespace'
            ]
        ]);

        $section = new Section();

        $section->setName($sectionName);
        $section->setHandle($sectionHandle);
        $section->setConfig($sectionConfig->toArray());
        $section->setVersion(1);
        $section->setCreated(new \DateTime());
        $section->setUpdated(new \DateTime());

        return $section;
    }

//    private function givenAQueryWithResult($result)
//    {
//        $query = Mockery::mock(AbstractQuery::class);
//        $query->shouldReceive('getResult')
//            ->once()
//            ->andReturn($result);
//
//        return $query;
//    }
//
    private function givenAFieldWithNameKindAndTo($name, $kind, $to)
    {
        $fieldName = 'field' . $name;
        $fieldHandle = 'field' . $name;
        $field = new Field();
        $field->setName($fieldName);
        $field->setHandle($fieldHandle);

        $fieldConfig = FieldConfig::fromArray([
            'field' => [
                'name' => $fieldName,
                'handle' => $fieldHandle,
                'kind' => $kind,
                'to' => 'section' . $to,
                'relationship-type' => 'bidirectional'
            ]
        ]);

        $field->setConfig($fieldConfig->toArray());
        $fieldType = new FieldType();
        $fieldType->setFullyQualifiedClassName('\\My\\Namespace\\FieldTypeClass' . $name);
        $fieldType->setType('string');
        $field->setFieldType($fieldType);

        return $field;
    }

    private function givenRelationshipsOfAll()
    {
        return [
            'sectionOne' => [
                'fieldOne' => [
                    'kind' => 'many-to-one',
                    'to' => 'sectionTwo',
                    'from' => 'sectionOne',
                    'fullyQualifiedClassName' => FullyQualifiedClassName::fromString(
                        '\\My\\Namespace\\FieldTypeClassOne'
                    ),
                    'relationship-type' => 'unidirectional'
                ],
                'fieldTwo-opposite' => [
                    'kind' => 'many-to-one',
                    'to' => 'sectionOne',
                    'fullyQualifiedClassName' => FullyQualifiedClassName::fromString(
                        '\\My\\Namespace\\FieldTypeClassTwo'
                    ),
                    'relationship-type' => 'bidirectional'
                ]
            ]
        ];
    }
}
