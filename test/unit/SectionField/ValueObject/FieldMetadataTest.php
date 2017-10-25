<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;
use Tardigrades\Helper\ArrayConverter;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\FieldMetadata
 * @covers ::<private>
 * @covers ::__construct
 */
class FieldMetadataTest extends TestCase
{
    /**
     * @test
     * @covers ::fromArray
     * @covers ::toArray
     */
    public function it_should_create_from_array()
    {
        $array = [
            'meta' => 'data',
            'data' => 'meta'
        ];
        $this->assertSame($array, FieldMetadata::fromArray($array)->toArray());
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $array = [
            'meta' => 'data',
            'data' => 'meta'
        ];

        $expected = "meta:data\n"
                  . "data:meta\n";

        $fieldMetadataString = (string)FieldMetadata::fromArray($array);
        $this->assertSame($expected, $fieldMetadataString);
    }
}
