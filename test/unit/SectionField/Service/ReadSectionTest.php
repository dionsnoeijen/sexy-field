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
use Tardigrades\Entity\Section;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Event\SectionEntryBeforeRead;
use Tardigrades\SectionField\Event\SectionDataRead;
use Tardigrades\SectionField\Event\SectionEntryDataRead;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\ValueObject\ApplicationConfig;
use Tardigrades\SectionField\ValueObject\Before;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\Service\LanguageManagerInterface;
use Tardigrades\SectionField\ValueObject\SectionConfig;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\ReadSection
 * @covers ::<private>
 * @covers ::__construct
 */
final class ReadSectionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var ReadSectionInterface|Mockery\MockInterface[] */
    private $readers;

    /** @var SectionManagerInterface|Mockery\MockInterface */
    private $sectionManager;

    /** @var EventDispatcherInterface|Mockery\MockInterface */
    private $dispatcher;

    /** @var ReadSection */
    private $readSection;

    public function setUp()
    {
        $this->readers = [Mockery::mock(ReadSectionInterface::class)];
        $this->sectionManager = Mockery::mock(SectionManagerInterface::class);
        $this->dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $this->readSection = new ReadSection(
            $this->readers,
            $this->sectionManager,
            $this->dispatcher
        );
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_successfully()
    {
        $sectionData = new \ArrayIterator();
        $section = $this->givenASectionWithName('One');
        $sectionOptionsBefore = $this->givenReadOptionsWithSection(['Section One']);
        $sectionOptionsData = $this->givenReadOptionsWithSection(
            FullyQualifiedClassName::fromString('My\\Namespace\\Entity\\SectionOne')
        );
        $sectionConfig = null;
        $this->readers[0]->shouldReceive('read')->once()->andReturn(new \ArrayIterator());
        $this->sectionManager->shouldReceive('readByHandle')->once()->andReturn($section);
        $this->dispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs([
                Mockery::on(
                    function ($sectionBeforeRead) use ($sectionData, $sectionOptionsBefore, $sectionConfig) {
                        if (!$sectionBeforeRead instanceof SectionEntryBeforeRead) {
                            return false;
                        }

                        $this->assertEquals($sectionData, $sectionBeforeRead->getData());
                        $this->assertEquals($sectionOptionsBefore, $sectionBeforeRead->getReadOptions());
                        $this->assertEquals($sectionConfig, $sectionBeforeRead->getSectionConfig());
                        return true;
                    }
                )
            ])
        ;

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs([
                Mockery::on(
                    function ($sectionDataRead) use ($sectionData, $sectionOptionsData, $section) {
                        if (!$sectionDataRead instanceof SectionDataRead) {
                            return false;
                        }

                        $this->assertEquals($sectionData, $sectionDataRead->getData());
                        $this->assertEquals($sectionOptionsData, $sectionDataRead->getReadOptions());
                        $this->assertEquals($section->getConfig(), $sectionDataRead->getSectionConfig());
                        return true;
                    }
                )
            ])
        ;

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs([
                Mockery::on(
                    function ($sectionEntryDataRead) use ($sectionData, $sectionOptionsBefore, $sectionConfig) {
                        if (!$sectionEntryDataRead instanceof SectionEntryDataRead) {
                            return false;
                        }
                        return true;
                    }
                )
            ])
        ;

        $result = $this->readSection->read($sectionOptionsBefore);
        $this->assertEquals(new \ArrayIterator(), $result);
    }

    /**
     * @test
     * @covers ::flush
     */
    public function it_should_flush()
    {
        $this->readers[0]->shouldReceive('flush')->once();
        $this->readSection->flush();
    }

    private function givenASectionWithName($name)
    {
        $sectionName = 'Section ' . $name;
        $sectionHandle = 'section' . $name;

        $sectionConfig = SectionConfig::fromArray([
            'section' => [
                'name' => $sectionName,
                'handle' => $sectionHandle,
                'fields' => [
                    'title',
                    'body',
                    'created'
                ],
                'slug' => ['title'],
                'default' => 'title',
                'namespace' => 'My\\Namespace'
            ]
        ]);

        $section = new Section();

        $section->setName($sectionName);
        $section->setHandle($sectionHandle);
        $section->setConfig($sectionConfig->toArray());
        $section->setVersion(1);
        $section->setCreated(new \DateTime());
        $section->setUpdated(new \DateTime());

        return $section;
    }

    private function givenReadOptionsWithSection($section)
    {
        $date = new \DateTime('2017-10-21T15:03');
        return ReadOptions::fromArray([
            'id' => 1,
            'slug' => 'section-one',
            'section' => $section,
            'sectionId' => 2,
            'limit' => 3,
            'offset' => 4,
            'orderBy' => ['some' => 'asc'],
            'before' => (string) Before::fromDateTime($date),
            'after' => (string) Before::fromDateTime($date),
            'localeEnabled' => true,
            'locale' => 'en_EN',
            'search' => 'search',
            'field' => ['color' => 'purple']
        ]);
    }
}
