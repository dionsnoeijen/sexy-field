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
        $this->assertSame('SexyMethod', (string) MethodName::fromString($string));
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $methodNameString = (string)MethodName::fromString('sexy method');
        $this->assertSame('SexyMethod', $methodNameString);

        $methodNameString = (string)MethodName::fromString('sexy-method');
        $this->assertSame('SexyMethod', $methodNameString);

        $methodNameString = (string)MethodName::fromString('sexy.method');
        $this->assertSame('Sexy.method', $methodNameString);

        $methodNameString = (string)MethodName::fromString('sexy_method');
        $this->assertSame('SexyMethod', $methodNameString);
    }
}
