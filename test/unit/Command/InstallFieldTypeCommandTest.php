<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\Service\FieldTypeManagerInterface;
use Tardigrades\SectionField\Service\FieldTypeNotFoundException;

/**
 * @coversDefaultClass Tardigrades\Command\InstallFieldTypeCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class InstallFieldTypeCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var FieldTypeManagerInterface|Mockery\MockInterface */
    private $fieldTypeManager;

    /** @var InstallFieldTypeCommand|Mockery\MockInterface */
    private $installFieldTypeCommand;

    /** @var Application */
    private $application;

    public function setUp(): void
    {
        $this->fieldTypeManager = Mockery::mock(FieldTypeManagerInterface::class);
        $this->installFieldTypeCommand = new InstallFieldTypeCommand($this->fieldTypeManager);
        $this->application = new Application();
        $this->application->add($this->installFieldTypeCommand);
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_install_a_field_type()
    {
        $command = $this->application->find('sf:install-field-type');
        $commandTester = new CommandTester($command);

        $this->fieldTypeManager
            ->shouldReceive('readByFullyQualifiedClassName')
            ->once()
            ->andThrow(FieldTypeNotFoundException::class);

        $this->fieldTypeManager
            ->shouldReceive('createWithFullyQualifiedClassName')
            ->once()
            ->andReturn(
                (new FieldType())
                ->setType('TextArea')
                ->setFullyQualifiedClassName('Some\\Fully\\Qualified\\Class\\Name')
                ->setCreated(new \DateTime())
                ->setUpdated(new \DateTime())
            );

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'namespace' => 'Some\\Fully\\Qualified\\Class\\Name'
            ]
        );

        $this->assertMatchesRegularExpression(
            '/FieldTypeInterface installed!/',
            $commandTester->getDisplay()
        );
    }
}
