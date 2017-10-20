<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Id
 * @covers ::<private>
 * @covers ::__construct
 */
class IdTest extends TestCase
{
    /**
     * @test
     * @covers ::fromInt
     * @covers ::getId
     * @covers ::toInt
     */
    public function it_should_create_from_int()
    {
        $thing = Id::fromInt(1234567890);
        $this->assertInstanceOf(Id::class, $thing);
        $this->assertSame($thing->toInt(),1234567890);
        $this->assertSame($thing->getId(),1234567890);
    }
}
