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
        $this->assertSame($array, FieldTypeGeneratorConfig::fromArray($array)->toArray());
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
        $this->assertSame('some sexy index:sexy content' . PHP_EOL, $fieldTypeGeneratorConfig);
    }
}
