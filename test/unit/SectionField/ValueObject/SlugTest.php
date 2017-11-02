<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Slug
 * @covers ::<private>
 * @covers ::__construct
 */
class SlugTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     * @covers ::toArray
     * @covers ::__toString
     */
    public function it_creates_from_string()
    {
        $sexySnail = Slug::fromString('this and that');
        $this->assertInstanceOf(Slug::class, $sexySnail);
        $this->assertSame(['this and that'], $sexySnail->toArray());
        $this->assertSame('this and that', (string) $sexySnail);
    }

    /**
     * @test
     * @covers ::create
     * @covers ::__toString
     */
    public function it_creates()
    {
        $sexySnail = Slug::create(['this', 'that']);
        $this->assertInstanceOf(Slug::class, $sexySnail);
        $this->assertSame('this-that', (string) $sexySnail);
    }

    /**
     * @test
     * @covers ::create
     */
    public function it_throws_exception_with_empty_array()
    {
        $this->expectException("InvalidArgumentException");
        $this->expectExceptionMessage('No slug elements defined');
        Slug::create([]);
    }
}
