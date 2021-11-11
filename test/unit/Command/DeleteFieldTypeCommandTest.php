<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\Field;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\Service\FieldTypeManagerInterface;
use Tardigrades\SectionField\Service\FieldTypeNotFoundException;

/**
 * @coversDefaultClass Tardigrades\Command\DeleteFieldTypeCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class DeleteFieldTypeCommandTest extends TestCase
{
    /** @var FieldTypeManagerInterface */
    private $fieldTypeManager;

    /** @var DeleteFieldTypeCommand */
    private $deleteFieldTypeCommand;

    /** @var Application */
    private $application;

    public function setUp(): void
    {
        $this->fieldTypeManager = Mockery::mock(FieldTypeManagerInterface::class);
        $this->deleteFieldTypeCommand = new DeleteFieldTypeCommand($this->fieldTypeManager);
        $this->application = new Application();
        $this->application->add($this->deleteFieldTypeCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_delete_field_type_with_id_1()
    {
        $command = $this->application->find('sf:delete-field');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfFieldTypes();

        $this->fieldTypeManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);

        $this->fieldTypeManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($fields[0]);

        $this->fieldTypeManager
            ->shouldReceive('delete')
            ->once();

        $commandTester->setInputs([1, 'y']);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression(
            '/Removed!/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_not_delete_field_type_with_id_1_when_cancelled()
    {
        $command = $this->application->find('sf:delete-field');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfFieldTypes();

        $this->fieldTypeManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);

        $this->fieldTypeManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($fields[0]);

        $this->fieldTypeManager
            ->shouldReceive('delete')
            ->never();

        $commandTester->setInputs([1, 'n']);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression(
            '/Cancelled, nothing deleted./',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_ask_to_delete_fields_first()
    {
        $command = $this->application->find('sf:delete-field');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfFieldTypesWithFields();

        $this->fieldTypeManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);

        $this->fieldTypeManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($fields[0]);

        $this->fieldTypeManager
            ->shouldReceive('delete')
            ->never();

        $commandTester->setInputs([1, 'n']);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression(
            '/This FieldType has fields that use this type, delete them first./',
            $commandTester->getDisplay()
        );
    }


    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_fail_with_invalid_field_type()
    {
        $command = $this->application->find('sf:delete-field');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfFieldTypesWithFields();

        $this->fieldTypeManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);

        $this->fieldTypeManager
            ->shouldReceive('read')
            ->once()
            ->andThrow(FieldTypeNotFoundException::class);

        $commandTester->setInputs([10]);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression(
            '/Field type not found/',
            $commandTester->getDisplay()
        );
    }

    private function givenAnArrayOfFieldTypes(): array
    {
        return [
            (new FieldType())
                ->setId(1)
                ->setType('TextArea')
                ->setFullyQualifiedClassName('Super\\Qualified')
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime()),
            (new FieldType())
                ->setId(2)
                ->setType('TextInput')
                ->setFullyQualifiedClassName('Amazing\\Input')
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
        ];
    }

    private function givenAnArrayOfFieldTypesWithFields(): array
    {
        $fieldOne = new Field();
        $fieldOne->setHandle('fieldOne');
        $fieldTwo = new Field();
        $fieldTwo->setHandle('fieldTwo');

        return [
            (new FieldType())
                ->setId(1)
                ->setType('TextArea')
                ->setFullyQualifiedClassName('Super\\Qualified')
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
                ->addField($fieldOne)
                ->addField($fieldTwo),
            (new FieldType())
                ->setId(2)
                ->setType('TextInput')
                ->setFullyQualifiedClassName('Amazing\\Input')
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
        ];
    }
}
