<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\After
 * @covers ::<private>
 * @covers ::__construct
 */
class AfterTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     */
    public function it_should_create_from_String()
    {
        $datetime = '2000-12-12T12:01';
        $after = After::fromString($datetime);
        $this->assertInstanceOf(After::class, $after);
    }

    /**
     * @test
     * @covers ::fromDateTime
     */
    public function it_should_create_from_DateTime()
    {
        $datetime = new \DateTime();
        $after = After::fromDateTime($datetime);
        $this->assertInstanceOf(After::class, $after);
    }

    /**
     * @test
     * @covers ::fromString
     */
    public function it_should_throw_exception_if_string_not_in_good_format()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage(
            'Date "two thousand-Dec-12T12:01" is invalid or does not match format "Y-m-d\TH:i"'
        );
        After::fromString('two thousand-Dec-12T12:01');
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $datetime = new \DateTime();
        $afterString = (string)After::fromDateTime($datetime);
        $dateString = $datetime->format('Y-m-d\TH:i');
        $this->assertSame($afterString, $dateString);
    }
}
