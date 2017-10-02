<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Mockery;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\Entity\Language;
use Tardigrades\Entity\SectionHistory;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\I18n;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\Version;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\DoctrineSectionHistoryManager
 * @covers ::<private>
 * @covers ::__construct
 */
final class SectionHistoryManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var DoctrineSectionHistoryManager
     */
    private $sectionHistoryManager;

    /**
     * @var EntityManagerInterface|Mockery\MockInterface
     */
    private $entityManager;

    public function setUp()
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->sectionHistoryManager = new DoctrineSectionHistoryManager(
            $this->entityManager
        );
    }

    /**
     * @test
     * @covers ::create
     */
    public function it_should_create()
    {
        $entity = new SectionHistory();
        $this->entityManager
            ->shouldReceive('persist')
            ->once()
            ->with($entity)
            ->andReturn($entity);

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $receive = $this->sectionHistoryManager->create($entity);

        $this->assertSame($receive, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_return_a_section_history()
    {
        $entity = new SectionHistory();
        $id = Id::fromInt(1);
        $sectionHistoryRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(SectionHistory::class)
            ->andReturn($sectionHistoryRepository);

        $sectionHistoryRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn($entity);

        $sectionHistory = $this->sectionHistoryManager->read($id);

        $this->assertEquals($sectionHistory, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_throw_an_exception()
    {
        $id = Id::fromInt(20);
        $sectionHistoryRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(SectionHistory::class)
            ->andReturn($sectionHistoryRepository);

        $sectionHistoryRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn(null);

        $this->expectException(SectionHistoryNotFoundException::class);

        $this->sectionHistoryManager->read($id);
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_section_histories()
    {
        $sectionHistoryOne = new SectionHistory();
        $sectionHistoryTwo = new SectionHistory();

        $sectionHistoryRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(SectionHistory::class)
            ->andReturn($sectionHistoryRepository);

        $sectionHistoryRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([
                $sectionHistoryOne,
                $sectionHistoryTwo
            ]);

        $this->assertEquals(
            $this->sectionHistoryManager->readAll(),
            [
                $sectionHistoryOne,
                $sectionHistoryTwo
            ]
        );
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_section_histories_and_throw_an_exception()
    {
        $sectionHistoryRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(SectionHistory::class)
            ->andReturn($sectionHistoryRepository);

        $sectionHistoryRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn(null);

        $this->expectException(SectionHistoryNotFoundException::class);

        $this->sectionHistoryManager->readAll();
    }

    /**
     * @test
     * @covers ::update
     */
    public function it_should_update_section_history()
    {
        $entity = new SectionHistory();
        $this->entityManager->shouldReceive('persist')->once()->with($entity);
        $this->entityManager->shouldReceive('flush')->once();

        $this->sectionHistoryManager->update($entity);
    }

    /**
     * @test
     * @covers ::delete
     */
    public function it_should_delete_a_section_history()
    {
        $entity = new SectionHistory();
        $this->entityManager->shouldReceive('remove')->once()->with($entity);
        $this->entityManager->shouldReceive('flush')->once();

        $this->sectionHistoryManager->delete($entity);
    }

    /**
     * @test
     * @covers ::readByHandleAndVersion
     */
    public function it_should_read_by_handle_and_version()
    {
        $handle = Handle::fromString('handle');
        $version = Version::fromInt(1);

        $sectionHistoryRepository = Mockery::mock(ObjectRepository::class);

        $entity = new SectionHistory();
        $entity->setHandle('handle');
        $entity->setVersion(1);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(SectionHistory::class)
            ->andReturn($sectionHistoryRepository);

        $sectionHistoryRepository
            ->shouldReceive('findBy')
            ->once()
            ->with(['handle' => 'handle', 'version' => 1])
            ->andReturn([$entity]);

        $returnedSectionHistory = $this->sectionHistoryManager->readByHandleAndVersion($handle, $version);

        $this->assertEquals($entity->getHandle(), $returnedSectionHistory->getHandle());
        $this->assertEquals($entity->getVersion(), $returnedSectionHistory->getVersion());
    }

    /**
     * @test
     * @covers ::readByHandleAndVersion
     */
    public function it_should_throw_exception_if_no_section_is_found_when_read_by_handle_and_version()
    {
        $this->expectException(SectionNotFoundException::class);
        $handle = Handle::fromString('handle');
        $version = Version::fromInt(1);

        $sectionHistoryRepository = Mockery::mock(ObjectRepository::class);

        $entity = new SectionHistory();
        $entity->setHandle('handle');
        $entity->setVersion(1);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(SectionHistory::class)
            ->andReturn($sectionHistoryRepository);

        $sectionHistoryRepository
            ->shouldReceive('findBy')
            ->once()
            ->with(['handle' => 'handle', 'version' => 1])
            ->andReturn(null);

        $this->sectionHistoryManager->readByHandleAndVersion($handle, $version);
    }
}
