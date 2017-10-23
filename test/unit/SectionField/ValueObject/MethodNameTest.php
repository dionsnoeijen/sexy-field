<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\MethodName
 * @covers ::<private>
 * @covers ::__construct
 */
class MethodNameTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     */
    public function it_creates_from_string()
    {
        $string = 'sexy method';
        $this->assertInstanceOf(MethodName::class, MethodName::fromString($string));
        $this->assertSame((string) MethodName::fromString($string), 'SexyMethod');
    }
}
