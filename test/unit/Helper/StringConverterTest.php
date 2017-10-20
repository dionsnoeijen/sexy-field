<?php
declare (strict_types = 1);

namespace Tardigrades\Helper;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\Helper\StringConverter
 */
final class StringConverterTest extends TestCase
{
    /**
     * @test
     * @covers ::toCamelCase
     */
    public function it_converts_a_string_to_camel_case()
    {
        $result = StringConverter::toCamelCase('this is a string 1 @ *** something else', ['@']);

        $this->assertSame('thisIsAString1@SomethingElse', $result);
    }

    /**
     * @test
     * @covers ::toSlug
     */
    public function it_converts_a_string_to_a_slug()
    {
        $result = StringConverter::toSlug('this is a string 1 @ *** something else');

        $this->assertSame('this-is-a-string-1-something-else', $result);
    }

    /**
     * @test
     * @covers ::toSlug
     */
    public function it_returns_n_a_when_string_is_empty_and_trying_to_convert_to_slug()
    {
        $result = StringConverter::toSlug('');

        $this->assertSame('n-a', $result);
    }
}
