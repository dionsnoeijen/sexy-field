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
use Tardigrades\Entity\Field;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\Service\FieldManagerInterface;
use Tardigrades\SectionField\Service\FieldNotFoundException;

/**
 * @coversDefaultClass Tardigrades\Command\ListFieldCommand
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class ListFieldCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var FieldManagerInterface */
    private $fieldManager;

    /** @var ListFieldCommand */
    private $listFieldCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp(): void
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->fieldManager = Mockery::mock(FieldManagerInterface::class);
        $this->listFieldCommand = new ListFieldCommand($this->fieldManager);
        $this->application = new Application();
        $this->application->add($this->listFieldCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_list_fields()
    {
        $yml = <<<YML
field:
    name: foo
    handle: bar
YML;

        file_put_contents($this->file, $yml);

        $command = $this->application->find('sf:list-field');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfFields();

        $this->fieldManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression(
            '/Some field name/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/Some other field name/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/And another field name/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/andAnotherName/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/TextInput/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/TextArea/',
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
            '/All installed Fields/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_fail_without_fields()
    {
        $command = $this->application->find('sf:list-field');
        $commandTester = new CommandTester($command);

        $this->fieldManager
            ->shouldReceive('readAll')
            ->once()
            ->andThrow(FieldNotFoundException::class);

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression(
            '/No fields found/',
            $commandTester->getDisplay()
        );
    }

    private function givenAnArrayOfFields()
    {
        return [
            (new Field())
                ->setId(1)
                ->setHandle('someName')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextInput')
                )
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setName('Some field name')
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new Field())
                ->setId(2)
                ->setHandle('someOtherName')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextArea')
                )
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setName('Some other field name')
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new Field())
                ->setId(3)
                ->setHandle('andAnotherName')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextArea')
                )
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setName('And another field name')
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
        ];
    }
}
