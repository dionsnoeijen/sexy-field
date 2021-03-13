<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Event;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\OptionsInterface;

/**
 * @coversDefaultClass Tardigrades\SectionField\Event\SectionEntryCreated
 * @covers ::__construct
 */
final class SectionEntryCreatedTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var SectionEntryCreated */
    private $sectionEntryCreated;

    /** @var CommonSectionInterface */
    private $entry;

    /** @var OptionsInterface */
    private $event;

    public function setUp(): void
    {
        $this->entry = Mockery::mock(CommonSectionInterface::class);
        $this->event = Mockery::mock(OptionsInterface::class);

        $this->sectionEntryCreated = new SectionEntryCreated(
            $this->entry,
            true,
            $this->event
        );
    }

    /**
     * @test
     * @covers ::getEntry
     */
    public function it_should_return_the_data()
    {
        $result = $this->sectionEntryCreated->getEntry();

        $this->assertEquals($this->entry, $result);
    }

    /**
     * @test
     * @covers ::getUpdate
     */
    public function it_should_return_the_type_of_action()
    {
        $result = $this->sectionEntryCreated->getUpdate();

        $this->assertEquals(true, $result);
    }
}
