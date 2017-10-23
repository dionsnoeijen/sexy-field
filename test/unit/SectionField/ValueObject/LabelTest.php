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
        $this->assertSame((string) $sexyLabel, 'labelicious');
    }
}
