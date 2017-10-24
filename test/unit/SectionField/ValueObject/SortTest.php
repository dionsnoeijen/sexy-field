<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Sort
 * @covers ::<private>
 * @covers ::__construct
 */
class SortTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     * @covers ::__construct
     * @covers ::__toString
     */
    public function it_creates_from_string()
    {
        $sort = Sort::fromString('asc');
        $this->assertInstanceOf(Sort::class, $sort);
        $this->assertSame(Sort::ASC, (string)$sort);
    }

    /**
     * @test
     * @covers ::fromString
     * @covers ::__construct
     */
    public function it_throws_exception_with_empty_string()
    {
        $this->expectException('InvalidArgumentException');
        Sort::fromString('');
    }

    /**
     * @test
     * @covers ::fromString
     * @covers ::__construct
     */
    public function it_throws_exception_with_invalid_string()
    {
        $this->expectException('InvalidArgumentException');
        Sort::fromString('oops');
    }

    /**
     * @test
     * @covers ::fromString
     * @covers ::__construct
     */
    public function it_throws_exception_with_capital_string()
    {
        $this->expectException('InvalidArgumentException');
        Sort::fromString('ASC');
    }
}
