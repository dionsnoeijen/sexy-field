<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Version
 * @covers ::<private>
 * @covers ::__construct
 */
class VersionTest extends TestCase
{
    /**
     * @test
     * @covers ::fromInt
     * @covers ::toInt
     */
    public function it_creates_from_int()
    {
        $sexyVersion = Version::fromInt(42);
        $this->assertInstanceOf(Version::class, $sexyVersion);
        $this->assertSame(42, $sexyVersion->toInt());
    }
}
