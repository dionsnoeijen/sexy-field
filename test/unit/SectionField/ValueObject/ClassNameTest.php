<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\ClassName
 * @covers ::<private>
 * @covers ::__construct
 */
class ClassNameTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     * @covers ::__toString
     */
    public function it_should_create_from_string()
    {
        $string = 'a sexy camel';
        $propertyName = ClassName::fromString($string);
        $this->assertInstanceOf(ClassName::class, $propertyName);
        $this->assertSame('ASexyCamel', (string) $propertyName);
    }
    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $propertyNameString = (string)ClassName::fromString('a sexy camel');
        $this->assertSame('ASexyCamel', $propertyNameString);

        $propertyNameString = (string)ClassName::fromString('a-sexy-camel');
        $this->assertSame('ASexyCamel', $propertyNameString);

        $propertyNameString = (string)ClassName::fromString('a_sexy_camel');
        $this->assertSame('ASexyCamel', $propertyNameString);

        $propertyNameString = (string)ClassName::fromString('a.sexy.camel');
        $this->assertSame('A.sexy.camel', $propertyNameString);
    }

}
