<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Name
 * @covers ::<private>
 * @covers ::__construct
 */
class NameTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     */
    public function it_creates_from_string()
    {
        $string = 'sexy name';
        $this->assertInstanceOf(Name::class, Name::fromString($string));
        $this->assertSame($string, (string) Name::fromString($string));
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $string = 'sexy name';
        $thing = (string)Name::fromString($string);

        $this->assertSame($string, $thing);
    }
}