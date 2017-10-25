<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Handle
 * @covers ::<private>
 * @covers ::__construct
 */
class HandleTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     */
    public function it_should_create_from_string()
    {
        $thing = Handle::fromString('wheeeeeee! handles!');
        $this->assertInstanceOf(Handle::class, $thing);
        $this->assertSame((string)$thing, 'wheeeeeee! handles!');
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $string = 'wheeeeeee! handles!';
        $thing = (string)Handle::fromString($string);

        $this->assertSame($string, $thing);
    }
}
