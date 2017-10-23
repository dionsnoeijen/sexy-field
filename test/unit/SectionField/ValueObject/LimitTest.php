<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Limit
 * @covers ::<private>
 * @covers ::__construct
 */
class LimitTest extends TestCase
{
    /**
     * @test
     * @covers ::fromInt
     * @covers ::toInt
     */
    public function it_creates_from_integer()
    {
        $limit = Limit::fromInt(666);
        $this->assertInstanceOf(Limit::class, $limit);
        $this->assertSame($limit->toInt(), 666);
    }
}
