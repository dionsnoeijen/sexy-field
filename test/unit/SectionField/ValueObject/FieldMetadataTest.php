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
        $this->assertSame(FieldMetadata::fromArray($array)->toArray(),$array);
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

        $fieldMetadataString = (string)FieldMetadata::fromArray($array);
        $string = ArrayConverter::recursive($array);
        $this->assertSame($fieldMetadataString, $string);
    }
}
