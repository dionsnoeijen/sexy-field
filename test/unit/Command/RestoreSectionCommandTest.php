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
use Tardigrades\SectionField\Service\SectionHistoryManagerInterface;

/**
 * @coversDefaultClass Tardigrades\Command\RestoreSectionCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class RestoreSectionCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var SectionManagerInterface */
    private $sectionManager;

    /** @var SectionHistoryManagerInterface */
    private $sectionHistoryManager;

    /** @var RestoreSectionCommand */
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

//    /**
//     * @test
//     * @covers ::configure
//     * @covers ::execute
//     */
//    public function it_should_restore_a_section()
//    {
//        $yml = <<<YML
//section:
//    name: foo
//    handle: bar
//    fields: []
//    default: Default
//    namespace: My\Namespace
//YML;
//
//        file_put_contents($this->file, $yml);
//
//        $command = $this->application->find('sf:restore-section');
//        $commandTester = new CommandTester($command);
//
//        $this->sectionManager
//            ->shouldReceive('readAll')
//            ->once()
//            ->andReturn($this->givenAnArrayOfSections());
//
////        $this->sectionHistoryManager
////            ->shouldReceive('read')
////            ->once()
////            ->with(1)
////            ->andReturn($this->givenAnArrayOfSections()[0]);
//
//        $this->sectionManager
//            ->shouldReceive('read')
//            ->once()
//            ->with(1)
//            ->andReturn($this->givenAnArrayOfSections()[0]);
//
//        $commandTester->setInputs([1, 'y']);
//        $commandTester->execute(
//            [
//                'command' => $command->getName()
//            ]
//        );

//        $this->assertRegExp(
//            '/Section updated!/',
//            $commandTester->getDisplay()
//        );
//    }

//    /**
//     * @test
//     * @covers ::configure
//     * @covers ::execute
//     */
//    public function it_should_fail_on_incorrect_config()
//    {
//        $yml = <<<YML
//section:
//    name: foo
//    handle: bar
//    fields: []
//    default: Default
//    namespace: My\Namespace
//YML;
//
//        $wrongYml = <<<YML
//wrong: yml
//YML;
//
//        file_put_contents($this->file, $yml);
//        $wrongConfig = vfsStream::url('home/wrong-config-file.yml');
//        file_put_contents($wrongConfig, $wrongYml);
//
//        $command = $this->application->find('sf:update-section');
//        $commandTester = new CommandTester($command);
//
//        $this->sectionManager
//            ->shouldReceive('readAll')
//            ->once()
//            ->andReturn($this->givenAnArrayOfSections());
//
//        $this->sectionManager
//            ->shouldReceive('read')
//            ->once()
//            ->andReturn($this->givenAnArrayOfSections()[0]);
//
//        $commandTester->setInputs([1]);
//        $commandTester->execute(
//            [
//                'command' => $command->getName(),
//                'config' => $wrongConfig
//            ]
//        );
//
//        $this->assertRegExp(
//            '/Invalid configuration/',
//            $commandTester->getDisplay()
//        );
//    }
//
//    private function givenAnArrayOfSections()
//    {
//        return [
//            (new Section())
//                ->setId(1)
//                ->setName('Some name')
//                ->setHandle('someHandle')
//                ->setConfig(
//                    Yaml::parse(
//                        file_get_contents($this->file)
//                    )
//                )
//                ->setCreated(new \DateTime())
//                ->setUpdated(new \DateTime())
//                ->setVersion(1),
//            (new Section())
//                ->setId(2)
//                ->setName('Some other name')
//                ->setHandle('someOtherHandle')
//                ->setConfig(
//                    Yaml::parse(
//                        file_get_contents($this->file)
//                    )
//                )
//                ->setCreated(new \DateTime())
//                ->setUpdated(new \DateTime())
//                ->setVersion(1),
//        ];
//    }
}
