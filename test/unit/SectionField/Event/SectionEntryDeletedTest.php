<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Event;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\OptionsInterface;

/**
 * @coversDefaultClass Tardigrades\SectionField\Event\SectionEntryDeleted
 * @covers ::__construct
 */
final class SectionEntryDeletedTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var SectionEntryDeleted */
    private $sectionEntryDeleted;

    /** @var CommonSectionInterface */
    private $entry;

    /** @var OptionsInterface */
    private $options;

    public function setUp(): void
    {
        $this->entry = Mockery::mock(CommonSectionInterface::class);
        $this->options = Mockery::mock(OptionsInterface::class);
        $this->sectionEntryDeleted = new SectionEntryDeleted(
            $this->entry,
            true,
            $this->options
        );
    }

    /**
     * @test
     * @covers ::getEntry
     */
    public function it_should_return_the_data()
    {
        $result = $this->sectionEntryDeleted->getEntry();

        $this->assertEquals($this->entry, $result);
    }

    /**
     * @test
     * @covers ::getSuccess
     */
    public function it_should_return_if_deletion_was_successful()
    {
        $result = $this->sectionEntryDeleted->getSuccess();

        $this->assertEquals(true, $result);
    }
}
