<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Before
 * @covers ::<private>
 * @covers ::__construct
 */
class BeforeTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     */
    public function it_should_create_from_String()
    {
        $datetime = '2000-12-12T12:01';
        $before = Before::fromString($datetime);
        $this->assertInstanceOf(Before::class, $before);
    }

    /**
     * @test
     * @covers ::fromDateTime
     */
    public function it_should_create_from_DateTime()
    {
        $datetime = new \DateTime();
        $before = Before::fromDateTime($datetime);
        $this->assertInstanceOf(Before::class, $before);
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
        Before::fromString('two thousand-Dec-12T12:01');
    }
}
