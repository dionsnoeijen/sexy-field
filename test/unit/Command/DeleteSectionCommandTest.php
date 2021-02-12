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
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\Service\SectionNotFoundException;

/**
 * @coversDefaultClass Tardigrades\Command\DeleteSectionCommand
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 * @covers Tardigrades\Command\SectionCommand::__construct
 */
final class DeleteSectionCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var SectionManagerInterface|Mockery\MockInterface */
    private $sectionManager;

    /** @var DeleteSectionCommand|Mockery\MockInterface */
    private $deleteSectionCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp(): void
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->sectionManager = Mockery::mock(SectionManagerInterface::class);
        $this->deleteSectionCommand = new DeleteSectionCommand($this->sectionManager);
        $this->application = new Application();
        $this->application->add($this->deleteSectionCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_delete_section_with_id_1()
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

        $command = $this->application->find('sf:delete-section');
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

        $this->sectionManager
            ->shouldReceive('delete')
            ->once();

        $commandTester->setInputs([1, 'y']);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/Removed!/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_delete_all_sections()
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

        $command = $this->application->find('sf:delete-section');
        $commandTester = new CommandTester($command);

        $sections = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->twice()
            ->andReturn($sections);

        $this->sectionManager
            ->shouldReceive('read')
            ->never();

        $this->sectionManager
            ->shouldReceive('delete')
            ->times(3);

        $commandTester->setInputs(['all', 'y', 'y', 'y']);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/Record with id #1 will be deleted/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Record with id #2 will be deleted/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Record with id #3 will be deleted/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Removed!/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_delete_section_with_id_1_and_3()
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

        $command = $this->application->find('sf:delete-section');
        $commandTester = new CommandTester($command);

        $sections = $this->givenAnArrayOfSections();

        $this->sectionManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($sections);

        $this->sectionManager
            ->shouldReceive('readByIds')
            ->once()
            ->andReturn([$sections[0], $sections[2]]);

        $this->sectionManager
            ->shouldReceive('delete')
            ->twice();

        $commandTester->setInputs(['1,3', 'y', 'y']);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertRegExp(
            '/Record with id #1 will be deleted/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Record with id #3 will be deleted/',
            $commandTester->getDisplay()
        );

        $this->assertRegExp(
            '/Removed!/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_fail_on_non_existing_id()
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

        $command = $this->application->find('sf:delete-section');
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

        $this->sectionManager
            ->shouldReceive('delete')
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
    public function it_should_not_delete_section_when_user_does_not_confirm()
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

        $command = $this->application->find('sf:delete-section');
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

        $this->sectionManager
            ->shouldNotReceive('delete');

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
