<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\AbstractQuery;
use Mockery;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\Entity\Language;
use Tardigrades\SectionField\ValueObject\I18n;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\LanguageConfig;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\DoctrineLanguageManager
 * @covers ::<private>
 * @covers ::__construct
 */
final class LanguageManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var DoctrineLanguageManager
     */
    private $languageManager;

    /**
     * @var EntityManagerInterface|Mockery\MockInterface
     */
    private $entityManager;

    public function setUp()
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->languageManager = new DoctrineLanguageManager(
            $this->entityManager
        );
    }

    /**
     * @test
     * @covers ::create
     */
    public function it_should_create()
    {
        $entity = new Language();
        $this->entityManager
            ->shouldReceive('persist')
            ->once()
            ->with($entity)
            ->andReturn($entity);

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $receive = $this->languageManager->create($entity);

        $this->assertSame($receive, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_return_a_language()
    {
        $entity = new Language();
        $id = Id::fromInt(1);
        $languageRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Language::class)
            ->andReturn($languageRepository);

        $languageRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn($entity);

        $language = $this->languageManager->read($id);

        $this->assertEquals($language, $entity);
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
            ->with(Language::class)
            ->andReturn($applicationRepository);

        $applicationRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn(null);

        $this->expectException(LanguageNotFoundException::class);

        $this->languageManager->read($id);
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_languages()
    {
        $languageOne = new Language();
        $languageTwo = new Language();

        $languageRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Language::class)
            ->andReturn($languageRepository);

        $languageRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([
                $languageOne,
                $languageTwo
            ]);

        $this->assertEquals(
            $this->languageManager->readAll(),
            [
                $languageOne,
                $languageTwo
            ]
        );
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_languages_and_throw_an_exception()
    {
        $languageRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Language::class)
            ->andReturn($languageRepository);

        $languageRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn(null);

        $this->expectException(LanguageNotFoundException::class);

        $this->languageManager->readAll();
    }

    /**
     * @test
     * @covers ::update
     */
    public function it_should_update_language()
    {
        $this->entityManager->shouldReceive('flush')->once();

        $this->languageManager->update();
    }

    /**
     * @test
     * @covers ::delete
     */
    public function it_should_delete_a_language()
    {
        $language = new Language();
        $this->entityManager->shouldReceive('remove')->once()->with($language);
        $this->entityManager->shouldReceive('flush')->once();

        $this->languageManager->delete($language);
    }

    /**
     * @test
     * @covers ::readByI18n
     */
    public function it_should_read_by_i18n()
    {
        $i18n = I18n::fromString('nl_NL');

        $languageRepository = Mockery::mock(ObjectRepository::class);

        $language = (new Language())->setI18n((string) $i18n);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Language::class)
            ->andReturn($languageRepository);

        $languageRepository
            ->shouldReceive('findOneBy')
            ->once()
            ->with(['i18n' => (string) $i18n])
            ->andReturn($language);

        $returnedLanguage = $this->languageManager->readByI18n($i18n);

        $this->assertEquals($language->getI18n(), $returnedLanguage->getI18n());
    }

    /**
     * @test
     * @covers ::readByI18n
     */
    public function it_should_throw_exception_when_language_not_found_when_reading_by_i18n()
    {
        $this->expectException(LanguageNotFoundException::class);

        $i18n = I18n::fromString('nl_NL');

        $languageRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Language::class)
            ->andReturn($languageRepository);

        $languageRepository
            ->shouldReceive('findOneBy')
            ->once()
            ->with(['i18n' => (string) $i18n]);

        $this->languageManager->readByI18n($i18n);
    }

    /**
     * @test
     * @covers ::readByI18ns
     */
    public function it_should_read_by_i18ns()
    {
        $i18n = I18n::fromString('nl_NL');

        $query = Mockery::mock(AbstractQuery::class);

        $language = (new Language())->setI18n((string) $i18n);

        $this->entityManager
            ->shouldReceive('createQuery')
            ->once()
            ->andReturn($query);

        $query->shouldReceive('getResult')->once()->andReturn([$language]);

        $returnedLanguages = $this->languageManager->readByI18ns([$i18n]);

        $this->assertEquals($language->getI18n(), $returnedLanguages['nl_NL']->getI18n());
    }

    /**
     * @test
     * @covers ::readByI18ns
     */
    public function it_should_throw_exception_when_no_results_when_reading_by_i18ns()
    {
        $this->expectException(LanguageNotFoundException::class);

        $i18n = I18n::fromString('nl_NL');

        $query = Mockery::mock(AbstractQuery::class);

        $this->entityManager
            ->shouldReceive('createQuery')
            ->once()
            ->andReturn($query);

        $query->shouldReceive('getResult')->once()->andReturn([]);

        $this->languageManager->readByI18ns([$i18n]);
    }

    /**
     * @test
     * @covers ::createByConfig
     * @covers ::setUpByConfig
     */
    public function it_should_create_by_config()
    {
        $configArray = [
            'language' => ['nl_NL']
        ];

        $config = LanguageConfig::fromArray($configArray);

        $query = Mockery::mock(AbstractQuery::class);

        $this->entityManager
            ->shouldReceive('createQuery')
            ->once()
            ->with("SELECT language FROM Tardigrades\Entity\Language language WHERE language.i18n IN ('nl_NL')")
            ->andReturn($query);

        $language = new Language();
        $language->setI18n('nl_NL');

        $query->shouldReceive('getResult')->once()->andReturn([$language]);

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $receive = $this->languageManager->createByConfig($config);

        $this->assertSame($this->languageManager, $receive);
    }

    /**
     * @test
     * @covers ::updateByConfig
     * @covers ::setUpByConfig
     */
    public function it_should_update_by_config()
    {
        $configArray = [
            'language' => ['nl_NL']
        ];

        $config = LanguageConfig::fromArray($configArray);

        $query = Mockery::mock(AbstractQuery::class);

        $this->entityManager
            ->shouldReceive('createQuery')
            ->once()
            ->with("SELECT language FROM Tardigrades\Entity\Language language WHERE language.i18n IN ('nl_NL')")
            ->andReturn($query);

        $language = new Language();
        $language->setI18n('nl_NL');

        $query->shouldReceive('getResult')->once()->andReturn([$language]);

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $receive = $this->languageManager->updateByConfig($config);

        $this->assertSame($this->languageManager, $receive);
    }

    /**
     * @test
     * @covers ::updateByConfig
     * @covers ::setUpByConfig
     */
    public function it_should_not_throw_exception_when_language_not_found_when_updating_by_config()
    {
        $configArray = [
            'language' => []
        ];

        $config = LanguageConfig::fromArray($configArray);

        $query = Mockery::mock(AbstractQuery::class);

        $this->entityManager
            ->shouldReceive('createQuery')
            ->once()
            ->with("SELECT language FROM Tardigrades\Entity\Language language WHERE language.i18n IN ()")
            ->andReturn($query);

        $language = new Language();
        $language->setI18n('nl_NL');

        $query->shouldReceive('getResult')->once();

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $this->languageManager->updateByConfig($config);
    }
}
