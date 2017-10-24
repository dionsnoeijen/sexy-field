<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Label
 * @covers ::<private>
 * @covers ::__construct
 */
class LabelTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     */
    public function it_should_create_from_string()
    {
        $sexyLabel = Label::fromString('labelicious');
        $this->assertInstanceOf(Label::class, $sexyLabel);
        $this->assertSame('labelicious', (string) $sexyLabel);
    }


    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $string = 'labelicious';
        $thing = (string)Label::fromString($string);

        $this->assertSame($string, $thing);
    }
}
