<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\PropertyName
 * @covers ::<private>
 * @covers ::__construct
 */
class PropertyNameTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     */
    public function it_should_create_from_string()
    {
        $string = 'a sexy camel';
        $propertyName = PropertyName::fromString($string);
        $this->assertInstanceOf(PropertyName::class, $propertyName);
        $this->assertSame((string) $propertyName, 'aSexyCamel');
    }
    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $propertyNameString = (string)PropertyName::fromString('a sexy camel');
        $this->assertSame('aSexyCamel', $propertyNameString);

        $propertyNameString = (string)PropertyName::fromString('a-sexy-camel');
        $this->assertSame('aSexyCamel', $propertyNameString);

        $propertyNameString = (string)PropertyName::fromString('a_sexy_camel');
        $this->assertSame('aSexyCamel', $propertyNameString);

        $propertyNameString = (string)PropertyName::fromString('a.sexy.camel');
        $this->assertSame('aSexyCamel', $propertyNameString);
    }
}
