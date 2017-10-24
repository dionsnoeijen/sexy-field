<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\SlugField
 * @covers ::<private>
 * @covers ::__construct
 */
class SlugFieldTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     * @covers ::__construct
     * @covers ::__toString
     */
    public function it_creates_from_string()
    {
        $string = 'this and that';
        $sexySnail = SlugField::fromString($string);
        $this->assertInstanceOf(SlugField::class,$sexySnail);
        $this->assertSame($string, (string) $sexySnail);
    }

    /**
     * @test
     * @covers ::fromString
     * @covers ::__construct
     */
    public function it_throws_exception_with_empty_string()
    {
        $this->expectException('InvalidArgumentException');
        SlugField::fromString('');
    }
}
