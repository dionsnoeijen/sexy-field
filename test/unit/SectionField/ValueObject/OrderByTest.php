<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\OrderBy
 * @covers ::<private>
 * @covers ::__construct
 */
class OrderByTest extends TestCase
{
    /**
     * @test
     * @covers ::fromHandleAndSort
     * @covers ::getSort
     * @covers ::getHandle
     * @covers ::toArray
     */
    public function it_creates_from_handler_and_sort()
    {
        $sexyHandle = Handle::fromString('handle');
        $sexySort = Sort::fromString(Sort::ASC);
        $orderBy = OrderBy::fromHandleAndSort($sexyHandle, $sexySort);
        $this->assertInstanceOf(OrderBy::class, $orderBy);
        $this->assertSame($sexySort, $orderBy->getSort());
        $this->assertSame($sexyHandle, $orderBy->getHandle());
        $this->assertSame([(string) $sexyHandle => (string) $sexySort], $orderBy->toArray());
    }
}
