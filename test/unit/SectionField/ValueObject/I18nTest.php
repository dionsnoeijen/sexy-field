<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\I18n
 * @covers ::<private>
 * @covers ::__construct
 */
class I18nTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     */
    public function it_should_create_from_string()
    {
        $thing = I18n::fromString('I am something which I do not really understand');
        $this->assertInstanceOf(I18n::class, $thing);
        $this->assertSame((string)$thing, 'I am something which I do not really understand');
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $string = 'wheeeéeeè!';
        $thing = (string)I18n::fromString($string);

        $this->assertSame($string, $thing);
    }
}
