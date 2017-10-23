<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

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
}
