<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;
use Tardigrades\Helper\ArrayConverter;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\FieldTypeGeneratorConfig
 * @covers ::<private>
 * @covers ::__construct
 */
class FieldTypeGeneratorConfigTest extends TestCase
{
    /**
     * @test
     * @covers ::fromArray
     * @covers ::toArray
     */
    public function it_should_create_from_array()
    {
        $array = [
            'some sexy index' => 'sexy content'
        ];
        $this->assertSame(FieldTypeGeneratorConfig::fromArray($array)->toArray(),$array);
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $array = [
            'some sexy index' => 'sexy content'
        ];

        $fieldTypeGeneratorConfig = (string)FieldTypeGeneratorConfig::fromArray($array);
        $string = ArrayConverter::recursive($array);
        $this->assertSame($fieldTypeGeneratorConfig, $string);
    }
}
