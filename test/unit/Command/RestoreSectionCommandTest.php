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
final class RestoreSectionCommandTest extends TestCase
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

    public function setUp()
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
        $this->assertRegExp(
            '/Config Restored!/',
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

        $this->assertRegExp(
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

        $this->assertRegExp(
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
        $this->assertRegExp(
            '/Cancelled/',
            $commandTester->getDisplay()
        );
    }

    private function givenAnArrayOfSections()
    {
        return [
            (new Section())
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
