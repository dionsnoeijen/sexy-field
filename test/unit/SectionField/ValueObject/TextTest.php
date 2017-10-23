<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Text
 * @covers ::<private>
 * @covers ::__construct
 */
class TextTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     */
    public function it_creates_from_string()
    {
        $sexyText = Text::fromString('sexy text');
        $this->assertInstanceOf(Text::class, $sexyText);
        $this->assertSame('sexy text', (string) $sexyText);
    }

    /**
     * @test
     * @covers ::fromString
     */
    public function it_throws_exception_if_empty()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Value is not specified');
        Text::fromString('');
    }
}
