<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Versioned
 * @covers ::<private>
 * @covers ::__construct
 */
class VersionedTest extends TestCase
{
    /**
     * @test
     * @covers ::fromDateTime
     * @covers ::getDateTime
     * @covers ::__construct
     * @covers ::__toString
     */
    public function it_should_create_from_DateTime()
    {
        $datetime = new \DateTime('2000-11-11T12:12:12');
        $versioned = Versioned::fromDateTime($datetime);
        $this->assertInstanceOf(Versioned::class, $versioned);
        $this->assertEquals(new \DateTime('2000-11-11T12:12:12'), $versioned->getDateTime());
        $this->assertEquals('2000-11-11T12:12', (string)$versioned);
    }
}
