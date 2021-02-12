<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\Section;
use Tardigrades\Entity\SectionHistory;
use Tardigrades\SectionField\Service\SectionHistoryManagerInterface;
use Tardigrades\SectionField\Service\SectionHistoryNotFoundException;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\Service\SectionNotFoundException;

/**
 * @coversDefaultClass Tardigrades\Command\RestoreSectionCommand
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 * @covers Tardigrades\Command\SectionCommand::__construct
 */
final class RestoreSectionCommandTest  extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var SectionManagerInterface|Mockery\MockInterface */
    private $sectionManager;

    /** @var SectionHistoryManagerInterface|Mockery\MockInterface */
    private $sectionHistoryManager;

    /** @var RestoreSectionCommand|Mockery\MockInterface */
    private $restoreSectionCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp(): void
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->sectionManager = Mockery::mock(SectionManagerInterface::class);
        $this->sectionHistoryManager = Mockery::mock(SectionHistoryManagerInterface::class);
        $this->restoreSectionCommand = new RestoreSectionCommand($this->sectionManager, $this->sectionHistoryManager);
        $this->application = new Application();
        $this->application->add($this->restoreSectionCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_restore_a_section()
    {
        $yml = <<<YML
section:
    name: foo
    handle: bar
    fields: []
    default: Default
    namespace: My\Namespace
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:restore-section');
        $commandTester = new CommandTester($command);

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($this->givenAnArrayOfSections());

        $this->sectionManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($this->givenAnArrayOfSections()[0]);

        $this->sectionHistoryManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($this->givenAnOldSection());

        $this->sectionManager
            ->shouldReceive('restoreFromHistory')
            ->once()
            ->andReturn($this->givenAnUpdatedSection());

        $commandTester->setInputs([1, 1, 'y']);
        $commandTester->execute(['command' => $command->getName()]);
        $this->assertMatchesRegularExpression(
            '/Config Restored!/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_restore_all_sections()
    {
        $yml = <<<YML
section:
    name: foo
    handle: bar
    fields: []
    default: Default
    namespace: My\Namespace
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:restore-section');
        $commandTester = new CommandTester($command);

        $this->sectionManager
            ->shouldReceive('readAll')
            ->twice()
            ->andReturn($this->givenAnArrayOfSections());

        $this->sectionManager
            ->shouldReceive('read')
            ->never();

        $this->sectionHistoryManager
            ->shouldReceive('read')
            ->twice()
            ->andReturn($this->givenAnOldSection());

        $this->sectionManager
            ->shouldReceive('restoreFromHistory')
            ->once();

        $commandTester->setInputs(['all', 1, 'y', 2, 'n']);

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression(
            '/Record with id #1 will be restored/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/Config Restored/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/Record with id #3 will be restored/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/Cancelled, nothing restored/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_restore_comma_separated_sections()
    {
        $yml = <<<YML
section:
    name: foo
    handle: bar
    fields: []
    default: Default
    namespace: My\Namespace
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:restore-section');
        $commandTester = new CommandTester($command);

        $sections = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($sections);

        $this->sectionManager
            ->shouldReceive('readByIds')
            ->once()
            ->with([1, 3, 4])
            ->andReturn([$sections[0], $sections[2], $sections[3]]);

        $this->sectionHistoryManager
            ->shouldReceive('read')
            ->twice()
            ->andReturn($this->givenAnOldSection());

        $this->sectionManager
            ->shouldReceive('restoreFromHistory')
            ->twice();

        $commandTester->setInputs(['1,3,4', 1, 'y', 2, 'y']);

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression(
            '/Record with id #1 will be restored/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/Config Restored/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/Record with id #3 will be restored/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/Skipped, no records can be found in history/',
            $commandTester->getDisplay()
        );
    }
    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_fail_on_non_existing_section()
    {
        $yml = <<<YML
section:
    name: foo
    handle: bar
    fields: []
    default: Default
    namespace: My\Namespace
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:restore-section');
        $commandTester = new CommandTester($command);

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($this->givenAnArrayOfSections());

        $this->sectionManager
            ->shouldReceive('read')
            ->once()
            ->andThrow(SectionNotFoundException::class);

        $this->sectionHistoryManager
            ->shouldReceive('read')
            ->never();

        $this->sectionManager
            ->shouldReceive('restoreFromHistory')
            ->never();

        $commandTester->setInputs([5]);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression(
            '/Section not found/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_fail_on_non_existing_version()
    {
        $yml = <<<YML
section:
    name: foo
    handle: bar
    fields: []
    default: Default
    namespace: My\Namespace
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:restore-section');
        $commandTester = new CommandTester($command);

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($this->givenAnArrayOfSections());

        $this->sectionManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($this->givenAnArrayOfSections()[0]);

        $this->sectionHistoryManager
            ->shouldReceive('read')
            ->once()
            ->andThrow(SectionHistoryNotFoundException::class);

        $this->sectionManager
            ->shouldReceive('restoreFromHistory')
            ->never();

        $commandTester->setInputs([1, 1]);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression(
            '/Section history not found/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_not_restore_a_section_when_user_does_not_confirm()
    {
        $yml = <<<YML
section:
    name: foo
    handle: bar
    fields: []
    default: Default
    namespace: My\Namespace
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:restore-section');
        $commandTester = new CommandTester($command);

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($this->givenAnArrayOfSections());

        $this->sectionManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($this->givenAnArrayOfSections()[0]);

        $this->sectionHistoryManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($this->givenAnOldSection());

        $this->sectionManager
            ->shouldNotReceive('restoreFromHistory');

        $commandTester->setInputs([1, 1, 'n']);
        $commandTester->execute(['command' => $command->getName()]);
        $this->assertMatchesRegularExpression(
            '/Cancelled/',
            $commandTester->getDisplay()
        );
    }

    private function givenAnArrayOfSections()
    {
        return [
            (new Section())
                ->setId(1)
                ->setName('Some name')
                ->setHandle('someHandle')
                ->setConfig(
                    Yaml::parse(
                        file_get_contents($this->file)
                    )
                )
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
                ->setVersion(2)
                ->addHistory((new SectionHistory())
                    ->setId(1)
                    ->setCreated(new \DateTime())
                    ->setUpdated(new \DateTime())
                    ->setName('Some neem')
                    ->setHandle('someHandle')
                    ->setConfig(
                        Yaml::parse(
                            file_get_contents($this->file)
                        )
                    )
                    ->setVersion(1))
                ->addHistory((new SectionHistory())
                    ->setId(2)
                    ->setCreated(new \DateTime())
                    ->setUpdated(new \DateTime())
                    ->setName('Some name')
                    ->setHandle('someHandle')
                    ->setConfig(
                        Yaml::parse(
                            file_get_contents($this->file)
                        )
                    )
                    ->setVersion(2)),
            (new Section())
                ->setId(2)
                ->setName('Some other name')
                ->setHandle('someOtherHandle')
                ->setConfig(
                    Yaml::parse(
                        file_get_contents($this->file)
                    )
                )
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
                ->setVersion(1),
            (new Section())
                ->setId(3)
                ->setName('Yet another name')
                ->setHandle('yetAnotherHandle')
                ->setConfig(
                    Yaml::parse(
                        file_get_contents($this->file)
                    )
                )
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
                ->setVersion(2)
                ->addHistory((new SectionHistory())
                    ->setId(3)
                    ->setCreated(new \DateTime())
                    ->setUpdated(new \DateTime())
                    ->setName('Yet another naem')
                    ->setHandle('yetAnotherHandle')
                    ->setConfig(
                        Yaml::parse(
                            file_get_contents($this->file)
                        )
                    )
                    ->setVersion(1))
                ->addHistory((new SectionHistory())
                    ->setId(4)
                    ->setCreated(new \DateTime())
                    ->setUpdated(new \DateTime())
                    ->setName('Yet another name')
                    ->setHandle('yetAnotherHandle')
                    ->setConfig(
                        Yaml::parse(
                            file_get_contents($this->file)
                        )
                    )
                    ->setVersion(2)),
            (new Section())
                ->setId(4)
                ->setName('A different name')
                ->setHandle('differentHandle')
                ->setConfig(
                    Yaml::parse(
                        file_get_contents($this->file)
                    )
                )
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
                ->setVersion(1),
        ];
    }

    private function givenAnOldSection()
    {
        return (new Section())
            ->setName('Some name')
            ->setHandle('someHandle')
            ->setConfig(
                Yaml::parse(
                    file_get_contents($this->file)
                )
            )
            ->setCreated(new \DateTime())
            ->setUpdated(new \DateTime())
            ->setVersion(1);
    }

    private function givenAnUpdatedSection()
    {
        return (new Section())
            ->setName('Some name')
            ->setHandle('someHandle')
            ->setConfig(
                Yaml::parse(
                    file_get_contents($this->file)
                )
            )
            ->setCreated(new \DateTime())
            ->setUpdated(new \DateTime())
            ->setVersion(3);
    }
}
