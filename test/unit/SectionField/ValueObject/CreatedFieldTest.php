<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\CreatedField
 * @covers ::<private>
 * @covers ::__construct
 */
class CreatedFieldTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     */
    public function it_should_create_from_String()
    {
        $this->assertInstanceOf(CreatedField::class, CreatedField::fromString('I am a created field!'));
    }

    /**
     * @test
     * @covers ::fromString
     */
    public function it_should_throw_exception_if_empty()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Value is not specified');
        CreatedField::fromString('');
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $string = 'I am a created field!';
        $createdFieldString = (string)CreatedField::fromString($string);
        $this->assertSame($createdFieldString, $string);
    }
}
