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
 * @coversDefaultClass Tardigrades\Command\DeleteFieldCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class DeleteFieldCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var FieldManagerInterface */
    private $fieldManager;

    /** @var DeleteFieldCommand */
    private $deleteFieldCommand;

    /** @var Application */
    private $application;

    /** @var vfsStream */
    private $file;

    public function setUp(): void
    {
        vfsStream::setup('home');
        $this->file = vfsStream::url('home/some-config-file.yml');
        $this->fieldManager = Mockery::mock(FieldManagerInterface::class);
        $this->deleteFieldCommand = new DeleteFieldCommand($this->fieldManager);
        $this->application = new Application();
        $this->application->add($this->deleteFieldCommand);

        $yml = <<<YML
field:
    name: foo
    handle: bar
    label: [ label ]
YML;

        file_put_contents($this->file, $yml);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_delete_field_with_id_1()
    {
        $command = $this->application->find('sf:delete-field');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfFields();

        $this->fieldManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);

        $this->fieldManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($fields[0]);

        $this->fieldManager
            ->shouldReceive('delete')
            ->once();

        $commandTester->setInputs([1, 'y']);
        $commandTester->execute([
            'command' => $command->getName()
        ]);

        $this->assertMatchesRegularExpression(
            '/Removed!/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::deleteWhatRecord
     */
    public function it_should_not_delete_field_when_user_does_not_confirm()
    {
        $command = $this->application->find('sf:delete-field');
        $commandTester = new CommandTester($command);
        $fields = $this->givenAnArrayOfFields();
        $this->fieldManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);
        $this->fieldManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($fields[0]);
        $this->fieldManager
            ->shouldNotReceive('delete');
        $commandTester->setInputs([1, 'n']);
        $commandTester->execute(['command' => $command->getName()]);
        $this->assertMatchesRegularExpression(
            '/Cancelled/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::execute
     * @covers ::deleteWhatRecord
     */
    public function it_should_not_try_to_delete_non_existing_fields()
    {
        $command = $this->application->find('sf:delete-field');
        $commandTester = new CommandTester($command);
        $fields = $this->givenAnArrayOfFields();
        $this->fieldManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);
        $this->fieldManager
            ->shouldReceive('read')
            ->andThrow(FieldNotFoundException::class);
        $commandTester->setInputs([9]);
        $commandTester->execute(['command' => $command->getName()]);
        $this->assertMatchesRegularExpression(
            '/Field not found/',
            $commandTester->getDisplay()
        );
    }

    private function givenAnArrayOfFields()
    {
        return [
            (new Field())
                ->setId(1)
                ->setHandle('someName')
                ->setName('Some name')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextInput')
                )
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new Field())
                ->setId(2)
                ->setHandle('someOtherName')
                ->setName('Some other name')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextArea')
                )
                ->setConfig([
                    'field' => [
                        'name' => 'Some other name',
                        'handle' => 'someOtherName',
                    ]
                ])
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new Field())
                ->setId(3)
                ->setHandle('andAnotherName')
                ->setName('And another name')
                ->setFieldType(
                    (new FieldType())
                        ->setName('TextArea')
                )
                ->setConfig(Yaml::parse(file_get_contents($this->file)))
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
        ];
    }
}
