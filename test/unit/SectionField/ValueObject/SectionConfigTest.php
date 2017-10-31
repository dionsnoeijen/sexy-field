<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\SectionConfig
 * @covers ::<private>
 * @covers ::__construct
 */
class SectionConfigTest extends TestCase
{
    /**
     * @test
     * @covers ::fromArray
     * @covers ::toArray
     * @covers ::getFields
     * @covers ::getName
     * @covers ::getHandle
     * @covers ::getClassName
     * @covers ::getNameSpace
     * @covers ::getGeneratorConfig
     * @covers ::getDefault
     * @covers ::getFullyQualifiedClassName
     */
    public function it_creates_from_array()
    {
        $sexyConfigArray = [
            'section' => [
                'name' => 'sexyName',
                'handle' => 'sexyHandles',
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'default' => 'Sexy per Default',
                'namespace' => 'sexy space',
                'generator' => 'Generator of awesome sexyness'
            ]
        ];
        $sexySectionConfig = SectionConfig::fromArray($sexyConfigArray);
        $this->assertInstanceOf(SectionConfig::class, $sexySectionConfig);
        $this->assertEquals($sexySectionConfig->getHandle(), Handle::fromString('sexyHandles'));
        $this->assertEquals($sexySectionConfig->getName(), Name::fromString('sexyName'));
        $this->assertEquals($sexySectionConfig->getClassName(), ClassName::fromString('sexyHandles'));
        $this->assertSame($sexySectionConfig->getFields(), ['s' => 'e', 'x' => 'y']);
        $this->assertEquals($sexySectionConfig->getNamespace(), SectionNamespace::fromString('sexy space'));
        $this->assertEquals(
            $sexySectionConfig->getGeneratorConfig(),
            GeneratorConfig::fromArray($sexyConfigArray['section'])
        );
        $this->assertSame($sexySectionConfig->getDefault(), $sexyConfigArray['section']['default']);
        $this->assertEquals(
            $sexySectionConfig->getFullyQualifiedClassName(),
            FullyQualifiedClassName::fromNamespaceAndClassName(
                $sexySectionConfig->getNamespace(),
                $sexySectionConfig->getClassName()
            )
        );
        $this->assertSame($sexySectionConfig->toArray(), $sexyConfigArray);
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getSlugField
     * @covers ::getCreatedField
     * @covers ::getUpdatedField
     */
    public function it_returns_default_fields_when_not_defined()
    {
        $sexyArray = [
            'section' => [
                'name' => 'sexyName',
                'handle' => 'sexyHandles',
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'default' => 'Sexy per Default',
                'namespace' => 'sexy space',]
        ];
        $sexySectionConfig = SectionConfig::fromArray($sexyArray);
        $this->assertEquals(CreatedField::fromString('created'), $sexySectionConfig->getCreatedField());
        $this->assertEquals(SlugField::fromString('slug'), $sexySectionConfig->getSlugField());
        $this->assertEquals(UpdatedField::fromString('updated'), $sexySectionConfig->getUpdatedField());
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getSlugField
     * @covers ::getCreatedField
     * @covers ::getUpdatedField
     */
    public function it_returns_default_fields_when_defined()
    {
        $sexyArray = [
            'section' => [
                'name' => 'sexyName',
                'handle' => 'sexyHandles',
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'default' => 'Sexy per Default',
                'namespace' => 'sexy space',
                'created' => 'just created',
                'slug' => 'sexy snail',
                'updated' => 'moar sexy now'
            ]
        ];
        $sexySectionConfig = SectionConfig::fromArray($sexyArray);
        $this->assertEquals($sexyArray['section']['created'], $sexySectionConfig->getCreatedField());
        $this->assertEquals($sexyArray['section']['slug'], $sexySectionConfig->getSlugField());
        $this->assertEquals($sexyArray['section']['updated'], $sexySectionConfig->getUpdatedField());
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getSlugField
     * @covers ::getCreatedField
     * @covers ::getUpdatedField
     */
    public function it_returns_default_fields_when_badly_defined()
    {
        $sexyArray = [
            'section' => [
                'name' => 'sexyName',
                'handle' => 'sexyHandles',
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'default' => 'Sexy per Default',
                'namespace' => 'sexy space',
                'created' => 123,
                'slug' => [],
                'updated' => CreatedField::fromString('created')
            ]
        ];
        $sexySectionConfig = SectionConfig::fromArray($sexyArray);
        $this->assertEquals(CreatedField::fromString('created'), $sexySectionConfig->getCreatedField());
        $this->assertEquals(SlugField::fromString('slug'), $sexySectionConfig->getSlugField());
        $this->assertEquals(UpdatedField::fromString('updated'), $sexySectionConfig->getUpdatedField());
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_section_is_not_present()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Config is not a section config');
        SectionConfig::fromArray(['a' => 'b']);
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_name_is_not_defined()
    {
        $sexyArray = [
            'section' => [
                'handle' => 'sexyHandles',
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'default' => 'Sexy per Default',
                'namespace' => 'sexy space'
            ]
        ];
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The config contains no section name');
        SectionConfig::fromArray($sexyArray);
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_name_is_empty()
    {
        $sexyArray = [
            'section' => [
                'name' => '',
                'handle' => 'sexyHandles',
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'default' => 'Sexy per Default',
                'namespace' => 'sexy space'
            ]
        ];
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The name is not defined');
        SectionConfig::fromArray($sexyArray);
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_name_is_not_string()
    {
        $sexyArray = [
            'section' => [
                'name' => ['a' => 'b'],
                'handle' => 'sexyHandles',
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'default' => 'Sexy per Default',
                'namespace' => 'sexy space'
            ]
        ];
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The name must be a string');
        SectionConfig::fromArray($sexyArray);
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_handle_is_not_defined()
    {
        $sexyArray = [
            'section' => [
                'name' => 'name',
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'default' => 'Sexy per Default',
                'namespace' => 'sexy space'
            ]
        ];
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The config contains no section handle');
        SectionConfig::fromArray($sexyArray);
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_handle_is_empty()
    {
        $sexyArray = [
            'section' => [
                'name' => 'name',
                'handle' => '',
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'default' => 'Sexy per Default',
                'namespace' => 'sexy space'
            ]
        ];
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The handle is not defined');
        SectionConfig::fromArray($sexyArray);
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_handle_is_not_string()
    {
        $sexyArray = [
            'section' => [
                'name' => 'sexy',
                'handle' => ['sexyHandles'],
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'default' => 'Sexy per Default',
                'namespace' => 'sexy space'
            ]
        ];
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The handle must be a string');
        SectionConfig::fromArray($sexyArray);
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_fields_is_not_defined()
    {
        $sexyArray = [
            'section' => [
                'name' => 'name',
                'handle' => 'handle',
                'default' => 'Sexy per Default',
                'namespace' => 'sexy space'
            ]
        ];
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The config contains no fields');
        SectionConfig::fromArray($sexyArray);
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_fields_are_not_array()
    {
        $sexyArray = [
            'section' => [
                'name' => 'name',
                'handle' => 'handle',
                'fields' => 'field',
                'default' => 'Sexy per Default',
                'namespace' => 'sexy space'
            ]
        ];
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Fields have to be defined as an array');
        SectionConfig::fromArray($sexyArray);
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_default_is_not_defined()
    {
        $sexyArray = [
            'section' => [
                'name' => 'name',
                'handle' => 'handle',
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'namespace' => 'sexy space'
            ]
        ];
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Assign a default field');
        SectionConfig::fromArray($sexyArray);
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_namespace_is_not_defined()
    {
        $sexyArray = [
            'section' => [
                'name' => 'name',
                'handle' => 'handle',
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'default' => 'Sexy per Default',
            ]
        ];
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('We do need a namespace');
        SectionConfig::fromArray($sexyArray);
    }
    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_namespace_is_not_a_string()
    {
        $sexyArray = [
            'section' => [
                'name' => 'name',
                'handle' => 'handle',
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'default' => 'Sexy per Default',
                'namespace' => 9999
            ]
        ];
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Namespace is not a string');
        SectionConfig::fromArray($sexyArray);
    }
    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_namespace_is_empty()
    {
        $sexyArray = [
            'section' => [
                'name' => 'name',
                'handle' => 'handle',
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'default' => 'Sexy per Default',
                'namespace' => ''
            ]
        ];
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The namespace value should not be empty');
        SectionConfig::fromArray($sexyArray);
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $sexyConfigArray = [
            'section' => [
                'name' => 'sexyName',
                'handle' => 'sexyHandles',
                'fields' => [
                    's' => 'e',
                    'x' => 'y'
                ],
                'default' => 'Sexy per Default',
                'namespace' => 'sexy space',
                'generator' => 'Generator of awesome sexyness'
            ]
        ];

        $expected = "name:sexyName\n"
                  . "handle:sexyHandles\n"
                  . "fields:\n"
                  . "- s:e\n"
                  . "- x:y\n"
                  . "default:Sexy per Default\n"
                  . "namespace:sexy space\n"
                  . "generator:Generator of awesome sexyness\n";

        $sexyConfigArrayString = (string)SectionConfig::fromArray($sexyConfigArray);
        $this->assertEquals($expected, $sexyConfigArrayString);
    }
}
