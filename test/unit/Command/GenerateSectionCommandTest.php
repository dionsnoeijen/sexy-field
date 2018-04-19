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
use Tardigrades\SectionField\Generator\GeneratorsInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\Service\SectionNotFoundException;

/**
 * @coversDefaultClass Tardigrades\Command\GenerateSectionCommand
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 * @covers Tardigrades\Command\SectionCommand::__construct
 */
final class GenerateSectionCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var SectionManagerInterface|Mockery\MockInterface */
    private $sectionManager;

    /** @var GeneratorsInterface|Mockery\MockInterface */
    private $entityGenerator;

    /** @var GenerateSectionCommand */
    private $generateSectionCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp()
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->sectionManager = Mockery::mock(SectionManagerInterface::class);
        $this->entityGenerator = Mockery::mock(GeneratorsInterface::class);
        $this->generateSectionCommand = new GenerateSectionCommand($this->sectionManager, $this->entityGenerator);
        $this->application = new Application();
        $this->application->add($this->generateSectionCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_generate_a_section()
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

        $command = $this->application->find('sf:generate-section');
        $commandTester = new CommandTester($command);

        $sections = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($sections);

        $this->sectionManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($sections[0]);

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->with($sections[0])
            ->once()
            ->andReturn([]);

        $this->entityGenerator
            ->shouldReceive('getBuildMessages')
            ->once();

        $commandTester->setInputs([1, 'y']);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/Some name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Some other name/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/someHandle/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/someOtherHandle/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/name:foo/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/handle:bar/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/fields:/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/default:Default/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/namespace:My\\\\Namespace/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Available sections/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_generate_all_sections_when_all_is_requested()
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

        $command = $this->application->find('sf:generate-section');
        $commandTester = new CommandTester($command);

        $sections = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->twice()
            ->andReturn($sections);

        $this->sectionManager
            ->shouldReceive('read')
            ->never();

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->with($sections[0]);

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->with($sections[1]);

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->with($sections[2]);

        $this->entityGenerator
            ->shouldReceive('getBuildMessages')
            ->once();

        $commandTester->setInputs(['all', 'y', 'y', 'y']);
        $commandTester->execute(['command' => $command->getName()]);
    }


    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_generate_all_sections_when_all_is_requested_without_asking_for_confirmation()
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

        $command = $this->application->find('sf:generate-section');
        $commandTester = new CommandTester($command);

        $sections = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->twice()
            ->andReturn($sections);

        $this->sectionManager
            ->shouldReceive('read')
            ->never();

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->with($sections[0]);

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->with($sections[1]);

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->with($sections[2]);

        $this->entityGenerator
            ->shouldReceive('getBuildMessages')
            ->once();

        $commandTester->setInputs(['all']);
        $commandTester->execute(['command' => $command->getName(), '--yes-mode' => null]);
    }


    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_generate_all_sections_when_all_is_requested_with_a_flag()
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

        $command = $this->application->find('sf:generate-section');
        $commandTester = new CommandTester($command);

        $sections = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->twice()
            ->andReturn($sections);

        $this->sectionManager
            ->shouldReceive('read')
            ->never();

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->with($sections[0]);

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->with($sections[1]);

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->with($sections[2]);

        $this->entityGenerator
            ->shouldReceive('getBuildMessages')
            ->once();

        $commandTester->execute(['command' => $command->getName(), '--yes-mode' => null, '--all' => null]);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_generate_selected_sections_when_requested_comma_separated()
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

        $command = $this->application->find('sf:generate-section');
        $commandTester = new CommandTester($command);

        $sections = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($sections);

        $this->sectionManager
            ->shouldReceive('readByIds')
            ->once()
            ->andReturn([$sections[0], $sections[1]]);

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->with($sections[0]);

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->with($sections[1]);

        $this->entityGenerator
            ->shouldReceive('getBuildMessages')
            ->once();

        $commandTester->setInputs(['1, 2', 'y', 'y']);
        $commandTester->execute(['command' => $command->getName()]);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_fail_when_section_is_not_found()
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

        $command = $this->application->find('sf:generate-section');
        $commandTester = new CommandTester($command);

        $sections = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($sections);

        $this->sectionManager
            ->shouldReceive('read')
            ->once()
            ->andThrow(SectionNotFoundException::class);

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->never();

        $this->entityGenerator
            ->shouldReceive('getBuildMessages')
            ->never();

        $commandTester->setInputs([1]);
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
    public function it_should_not_generate_a_section_when_user_does_not_confirm()
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

        $command = $this->application->find('sf:generate-section');
        $commandTester = new CommandTester($command);

        $sections = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($sections);

        $this->sectionManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($sections[0]);

        $this->entityGenerator
            ->shouldReceive('generateBySection')
            ->with($sections[0])
            ->once();

        $this->entityGenerator
            ->shouldNotReceive('getBuildMessages');

        $commandTester->setInputs([1, 'n']);
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
                ->setId(1)
                ->setName('Some name')
                ->setHandle('someHandle')
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
                ->setVersion(1),
            (new Section())
                ->setId(2)
                ->setName('Some other name')
                ->setHandle('someOtherHandle')
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
                ->setVersion(1),
            (new Section())
                ->setId(3)
                ->setName('Yet another name')
                ->setHandle('AnotherHandle')
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
                ->setVersion(1),
        ];
    }
}
