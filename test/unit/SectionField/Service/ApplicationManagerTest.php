<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Mockery;
use Mockery\Mock;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\Entity\Application;
use Tardigrades\Entity\Language;
use Tardigrades\SectionField\ValueObject\ApplicationConfig;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\Service\LanguageManagerInterface;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\DoctrineApplicationManager
 * @covers ::<private>
 * @covers ::__construct
 */
final class ApplicationManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var DoctrineApplicationManager */
    private $applicationManager;

    /** @var Mock|EntityManagerInterface */
    private $entityManager;

    /** @var Mock|LanguageManagerInterface */
    private $languageManager;

    public function setUp(): void
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->languageManager = Mockery::mock(LanguageManagerInterface::class);
        $this->applicationManager = new DoctrineApplicationManager(
            $this->entityManager,
            $this->languageManager
        );
    }

    /**
     * @test
     * @covers ::create
     */
    public function it_should_create()
    {
        $entity = new Application();
        $this->entityManager
            ->shouldReceive('persist')
            ->once()
            ->with($entity)
            ->andReturn($entity);

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $receive = $this->applicationManager->create($entity);

        $this->assertSame($receive, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_return_an_application()
    {
        $entity = new Application();
        $id = Id::fromInt(1);
        $applicationRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Application::class)
            ->andReturn($applicationRepository);

        $applicationRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn($entity);

        $application = $this->applicationManager->read($id);

        $this->assertEquals($application, $entity);
    }

    /**
     * @test
     * @covers ::readByHandle
     */
    public function it_should_read_by_handle()
    {
        $application = new Application;
        $applicationRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Application::class)
            ->andReturn($applicationRepository);

        $applicationRepository
            ->shouldReceive('findOneBy')
            ->once()
            ->with(['handle' => 'foo'])
            ->andReturn($application);

        $this->assertSame($application, $this->applicationManager->readByHandle(Handle::fromString('foo')));
    }

    /**
     * @test
     * @covers ::readByHandle
     */
    public function it_should_be_able_to_fail_when_reading_by_handle()
    {
        $this->expectException(ApplicationNotFoundException::class);

        $applicationRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Application::class)
            ->andReturn($applicationRepository);

        $applicationRepository
            ->shouldReceive('findOneBy')
            ->once()
            ->with(['handle' => 'foo'])
            ->andReturnNull();

        $this->applicationManager->readByHandle(Handle::fromString('foo'));
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_throw_an_exception()
    {
        $id = Id::fromInt(20);
        $applicationRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Application::class)
            ->andReturn($applicationRepository);

        $applicationRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn(null);

        $this->expectException(ApplicationNotFoundException::class);

        $this->applicationManager->read($id);
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_applications()
    {
        $applicationOne = new Application();
        $applicationTwo = new Application();

        $applicationRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Application::class)
            ->andReturn($applicationRepository);

        $applicationRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([$applicationOne, $applicationTwo]);

        $this->assertEquals(
            $this->applicationManager->readAll(),
            [$applicationOne, $applicationTwo]
        );
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_applications_and_throw_an_exception()
    {
        $applicationRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Application::class)
            ->andReturn($applicationRepository);

        $applicationRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn(null);

        $this->expectException(ApplicationNotFoundException::class);

        $this->applicationManager->readAll();
    }

    /**
     * @test
     * @covers ::update
     */
    public function it_should_update_an_application()
    {
        $this->entityManager->shouldReceive('flush')->once();

        $this->applicationManager->update();
    }

    /**
     * @test
     * @covers ::delete
     */
    public function it_should_delete_an_application()
    {
        $application = new Application();
        $this->entityManager->shouldReceive('remove')->once()->with($application);
        $this->entityManager->shouldReceive('flush')->once();

        $this->applicationManager->delete($application);
    }

    /**
     * @test
     * @covers ::createByConfig
     */
    public function it_should_create_by_config()
    {
        $configArray = [
            'application' => [
                'name' => 'Name',
                'handle' => 'handle',
                'languages' => ['en_EN']
            ]
        ];

        $config = ApplicationConfig::fromArray($configArray);

        $language = new Language();
        $entity = new Application();
        $entity->setName('Name');
        $entity->setHandle('handle');
        $entity->addLanguage($language);

        $this->entityManager
            ->shouldReceive('persist')
            ->once()
            ->andReturn($entity);

        $this->languageManager->shouldReceive('readByI18ns')->once()->andReturn([$language]);

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $receive = $this->applicationManager->createByConfig($config);

        $this->assertEquals($entity, $receive);
    }

    /**
     * @test
     * @covers ::updateByConfig
     */
    public function it_should_update_by_config()
    {
        $configArray = [
            'application' => [
                'name' => 'Name2',
                'handle' => 'handle2',
                'languages' => ['en_EN']
            ]
        ];

        $config = ApplicationConfig::fromArray($configArray);

        $language = new Language();
        $entity = new Application();
        $entity->setName('Name');
        $entity->setHandle('handle');
        $entity->addLanguage($language);

        $this->languageManager->shouldReceive('readByI18ns')->once()->andReturn([$language]);

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $receive = $this->applicationManager->updateByConfig($config, $entity);

        $this->assertEquals($entity, $receive);
    }
}
