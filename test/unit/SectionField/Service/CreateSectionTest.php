<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tardigrades\SectionField\Event\SectionEntryBeforeCreate;
use Tardigrades\SectionField\Event\SectionEntryCreated;
use Tardigrades\SectionField\Generator\CommonSectionInterface;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\CreateSection
 * @covers ::<private>
 * @covers ::__construct
 */
final class CreateSectionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var CreateSectionInterface|Mockery\MockInterface[] */
    private $creators;

    /** @var EventDispatcherInterface|Mockery\MockInterface */
    private $dispatcher;

    /** @var CreateSection */
    private $createSection;

    public function setUp()
    {
        $this->creators = [Mockery::mock(CreateSectionInterface::class)];
        $this->dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $this->createSection = new CreateSection(
            $this->creators,
            $this->dispatcher
        );
    }

    /**
     * @test
     * @covers ::save
     */
    public function it_should_save_new_section()
    {
        $entry = Mockery::mock(CommonSectionInterface::class);
        $entry->shouldReceive('getId')->once()->andReturn(null);
        $this->creators[0]->shouldReceive('save')->once();

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs([
                'section.entry.before.create',
                Mockery::on(
                    function ($sectionEntryBeforeCreate) {
                        if (!$sectionEntryBeforeCreate instanceof SectionEntryBeforeCreate) {
                            return false;
                        }

                        return true;
                    }
                )
            ]);

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs([
                'section.entry.created',
                Mockery::on(
                    function ($sectionEntryCreated) {
                        if (!$sectionEntryCreated instanceof SectionEntryCreated) {
                            return false;
                        }

                        $this->assertFalse($sectionEntryCreated->getUpdate());

                        return true;
                    }
                )
            ]);

        $this->createSection->save($entry);
    }

    /**
     * @test
     * @covers ::save
     */
    public function it_should_update_section()
    {
        $entry = Mockery::mock(CommonSectionInterface::class);
        $entry->shouldReceive('getId')->once()->andReturn(1);
        $this->creators[0]->shouldReceive('save')->once();

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs([
                'section.entry.before.create',
                Mockery::on(
                    function ($sectionEntryBeforeCreate) {
                        if (!$sectionEntryBeforeCreate instanceof SectionEntryBeforeCreate) {
                            return false;
                        }

                        return true;
                    }
                )
            ]);

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs([
                'section.entry.created',
                Mockery::on(
                    function ($sectionEntryCreated) {
                        if (!$sectionEntryCreated instanceof SectionEntryCreated) {
                            return false;
                        }

                        $this->assertTrue($sectionEntryCreated->getUpdate());

                        return true;
                    }
                )
            ]);

        $this->createSection->save($entry);
    }

    /**
     * @test
     * @covers ::persist
     */
    public function it_should_persist_section()
    {
        $entry = Mockery::mock(CommonSectionInterface::class);
        $this->creators[0]->shouldReceive('persist')->once();

        $this->createSection->persist($entry);
    }

    /**
     * @test
     * @covers ::flush
     */
    public function it_should_flush_creators()
    {
        $entry = Mockery::mock(CommonSectionInterface::class);
        $this->creators[0]->shouldReceive('flush')->once();

        $this->createSection->flush($entry);
    }
}
