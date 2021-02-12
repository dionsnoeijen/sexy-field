<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Mockery;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tardigrades\Entity\Application;
use Tardigrades\Entity\Language;
use Tardigrades\SectionField\Event\SectionEntryBeforeDelete;
use Tardigrades\SectionField\Event\SectionEntryDeleted;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\ValueObject\ApplicationConfig;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\Service\LanguageManagerInterface;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\DeleteSection
 * @covers ::<private>
 * @covers ::__construct
 */
final class DeleteSectionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var DeleteSectionInterface|Mockery\MockInterface[] */
    private $deleters;

    /** @var EventDispatcherInterface|Mockery\MockInterface */
    private $dispatcher;

    /** @var DeleteSection */
    private $deleteSection;

    public function setUp(): void
    {
        $this->deleters = [Mockery::mock(DeleteSectionInterface::class)];
        $this->dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $this->deleteSection = new DeleteSection(
            $this->deleters,
            $this->dispatcher
        );
    }

    /**
     * @test
     * @covers ::delete
     */
    public function it_should_delete_successfully()
    {
        $entry = Mockery::mock(CommonSectionInterface::class);
        $this->deleters[0]->shouldReceive('delete')->once()->andReturn(true);

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs([
                Mockery::on(
                    function ($sectionEntryBeforeDelete) {
                        if (!$sectionEntryBeforeDelete instanceof SectionEntryBeforeDelete) {
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
                    function ($sectionEntryDeleted) {
                        if (!$sectionEntryDeleted instanceof SectionEntryDeleted) {
                            return false;
                        }

                        $this->assertTrue($sectionEntryDeleted->getSuccess());
                        return true;
                    }
                )
            ]);

        $result = $this->deleteSection->delete($entry);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::delete
     */
    public function it_should_return_false_when_failing_to_delete_successfully()
    {
        $entry = Mockery::mock(CommonSectionInterface::class);
        $this->deleters[0]->shouldReceive('delete')->once()->andReturn(false);

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs([
                Mockery::on(
                    function ($sectionEntryBeforeDelete) {
                        if (!$sectionEntryBeforeDelete instanceof SectionEntryBeforeDelete) {
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
                    function ($sectionEntryDeleted) {
                        if (!$sectionEntryDeleted instanceof SectionEntryDeleted) {
                            return false;
                        }
                        $this->assertFalse($sectionEntryDeleted->getSuccess());
                        return true;
                    }
                )
            ]);

        $result = $this->deleteSection->delete($entry);
        $this->assertFalse($result);
    }

    /**
     * @test
     * @covers ::remove
     */
    public function it_should_remove_successfully()
    {
        $this->dispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs([
                Mockery::on(
                    function ($sectionEntryBeforeDelete) {
                        if (!$sectionEntryBeforeDelete instanceof SectionEntryBeforeDelete) {
                            return false;
                        }
                        return true;
                    }
                )
            ]);
        $entry = Mockery::mock(CommonSectionInterface::class);
        $this->deleters[0]->shouldReceive('remove')->once()->with($entry);
        $this->deleteSection->remove($entry);
    }

    /**
     * @test
     * @covers ::flush
     */
    public function it_should_flush_successfully()
    {
        $this->deleters[0]->shouldReceive('flush')->once();
        $this->deleteSection->flush();
    }
}
