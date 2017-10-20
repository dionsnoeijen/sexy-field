<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Event;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\Generator\CommonSectionInterface;

/**
 * @coversDefaultClass Tardigrades\SectionField\Event\SectionEntryBeforeCreate
 * @covers ::__construct
 */
final class SectionEntryBeforeCreateTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @test
     * @covers ::getEntry
     */
    public function it_should_get_the_entry()
    {
        $entry = Mockery::mock(CommonSectionInterface::class);

        $sectionEntryBeforeCreate = new SectionEntryBeforeCreate($entry);
        $result = $sectionEntryBeforeCreate->getEntry();

        $this->assertEquals($entry, $result);
    }
}
