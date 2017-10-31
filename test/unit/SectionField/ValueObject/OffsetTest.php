<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Offset
 * @covers ::<private>
 * @covers ::__construct
 */
class OffsetTest extends TestCase
{
    /**
     * @test
     * @covers ::fromInt
     * @covers ::toInt
     */
    public function it_creates_from_integer()
    {
        $offset = Offset::fromInt(120000);
        $this->assertInstanceOf(Offset::class, $offset);
        $this->assertSame(120000, $offset->toInt());
    }
}
