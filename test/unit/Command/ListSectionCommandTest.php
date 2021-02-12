<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\Service\SectionNotFoundException;

/**
 * @coversDefaultClass Tardigrades\Command\ListSectionCommand
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 * @covers Tardigrades\Command\SectionCommand::__construct
 */
final class ListSectionCommandTest  extends TestCase
{
    /** @var SectionManagerInterface */
    private $sectionManager;

    /** @var ListSectionCommand */
    private $listSectionCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp(): void
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->sectionManager = Mockery::mock(SectionManagerInterface::class);
        $this->listSectionCommand = new ListSectionCommand($this->sectionManager);
        $this->application = new Application();
        $this->application->add($this->listSectionCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_list_sections()
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

        $command = $this->application->find('sf:list-section');
        $commandTester = new CommandTester($command);

        $sections = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($sections);

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression(
            '/Some name/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/someHandle/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/name:foo/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/handle:bar/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/fields:/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/default:Default/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            "/namespace:My\\\\Namespace/",
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/Some other name/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/someOtherHandle/',
            $commandTester->getDisplay()
        );
        $this->assertMatchesRegularExpression(
            '/All installed Sections/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/All installed Sections/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/All installed Sections/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_fail_without_sections()
    {
        $command = $this->application->find('sf:list-section');
        $commandTester = new CommandTester($command);

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andThrow(SectionNotFoundException::class);

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression(
            '/No section found/',
            $commandTester->getDisplay()
        );
    }

    private function givenAnArrayOfSections()
    {
        return [
            (new Section())
                ->setName('Some name')
                ->setHandle('someHandle')
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
                ->setVersion(1),
            (new Section())
                ->setName('Some other name')
                ->setHandle('someOtherHandle')
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
                ->setVersion(1),
        ];
    }
}
