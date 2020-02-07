<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tardigrades\SectionField\Event\SectionEntryBeforeCreate;
use Tardigrades\SectionField\Event\SectionEntryBeforeUpdate;
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

    /** @var CacheInterface|Mockery\MockInterface */
    private $cache;

    public function setUp()
    {
        $this->creators = [Mockery::mock(CreateSectionInterface::class)];
        $this->dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $this->cache = Mockery::mock(CacheInterface::class);

        $this->createSection = new CreateSection(
            $this->creators,
            $this->dispatcher,
            $this->cache
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
        $this->cache->shouldReceive('invalidateForSection')->once();

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs([
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

        $this->cache->shouldReceive('invalidateForSection')->once();

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs([
                Mockery::on(
                    function ($sectionEntryBeforeUpdate) {
                        if (!$sectionEntryBeforeUpdate instanceof SectionEntryBeforeUpdate) {
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
        $entry->shouldReceive('getId')->once()->andReturn(null);
        $this->dispatcher->shouldReceive('dispatch')->once();
        $this->creators[0]->shouldReceive('persist')->once();
        $this->createSection->persist($entry);
    }

    /**
     * @test
     * @covers ::persist
     * @covers ::flush
     */
    public function it_should_flush_creators()
    {
        // We persist three entities of two different classes
        // So there should be three persists (for each entity), one flush, and two invalidations (for each class)
        $entry = Mockery::mock(CommonSectionInterface::class);
        $otherEntry = Mockery::mock(CommonerSectionInterface::class);
        $thirdEntry = Mockery::mock(CommonSectionInterface::class);

        $entry->shouldReceive('getId')->once()->andReturn(null);
        $otherEntry->shouldReceive('getId')->once()->andReturn(null);
        $thirdEntry->shouldReceive('getId')->once()->andReturn(null);

        $this->dispatcher->shouldReceive('dispatch')->times(3);
        $this->creators[0]->shouldReceive('persist')->times(3);
        $this->creators[0]->shouldReceive('flush')->once();
        $this->cache->shouldReceive('invalidateForSection')->twice();

        $this->createSection->persist($entry);
        $this->createSection->persist($otherEntry);
        $this->createSection->persist($thirdEntry);
        $this->createSection->flush();
    }
}

interface CommonerSectionInterface extends CommonSectionInterface {}
