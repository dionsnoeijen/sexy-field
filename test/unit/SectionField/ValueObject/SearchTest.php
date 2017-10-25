<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Search
 * @covers ::<private>
 * @covers ::__construct
 */
class SearchTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     */
    public function it_should_create_from_string()
    {
        $string = 'a sexy search';
        $search = Search::fromString($string);
        $this->assertInstanceOf(Search::class, $search);
        $this->assertSame($string, (string) $search);
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $string = 'a sexy search';
        $thing = (string)Search::fromString($string);
        $this->assertSame($string, $thing);
    }
}
