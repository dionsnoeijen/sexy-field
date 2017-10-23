<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Type
 * @covers ::<private>
 * @covers ::__construct
 */
class TypeTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     */
    public function it_creates_from_string()
    {
        $sexyType = Type::fromString('sexy type');
        $this->assertInstanceOf(Type::class, $sexyType);
        $this->assertSame('sexy type', (string) $sexyType);
    }
}
