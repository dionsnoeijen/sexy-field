<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\Language;
use Tardigrades\Entity\Application as ApplicationEntity;
use Tardigrades\SectionField\Service\DoctrineLanguageManager;
use Tardigrades\SectionField\Service\LanguageNotFoundException;

/**
 * @coversDefaultClass Tardigrades\Command\DeleteLanguageCommand
 * @covers ::<private>
 * @covers ::__construct
 */
final class DeleteLanguageCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var DoctrineLanguageManager|Mockery\MockInterface */
    private $languageManager;

    /** @var DeleteLanguageCommand */
    private $deleteLanguageCommand;

    /** @var Application */
    private $application;

    public function setUp(): void
    {
        $this->languageManager = Mockery::mock(DoctrineLanguageManager::class);
        $this->deleteLanguageCommand = new DeleteLanguageCommand($this->languageManager);
        $this->application = new Application();
        $this->application->add($this->deleteLanguageCommand);
    }

    private function givenAnArrayOfLanguages(): array
    {
        return [
            (new Language())
                ->setId(1)
                ->setI18n('nl_NL')
                ->addApplication(
                    (new ApplicationEntity())
                        ->setId(1)
                        ->setName('Application name')
                )
                ->addApplication(
                    (new ApplicationEntity())
                        ->setId(2)
                        ->setName('Another application name')
                )
                ->setUpdated(new \DateTime())
                ->setCreated(new \DateTime()),
            (new Language())
                ->setId(2)
                ->setI18n('en_EN')
                ->addApplication(
                    (new ApplicationEntity())
                        ->setId(1)
                        ->setName('Again, a name')
                )
                ->addApplication(
                    (new ApplicationEntity())
                        ->setId(2)
                        ->setName('Fffff, name')
                )
                ->setUpdated(new \DateTime())
                ->setCreated(new \DateTime()),
        ];
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_delete_lang_with_id_1()
    {
        $command = $this->application->find('sf:delete-language');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfLanguages();

        $this->languageManager
            ->shouldReceive('readAll')
            ->twice()
            ->andReturn($fields);

        $this->languageManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($fields[0]);

        $this->languageManager
            ->shouldReceive('delete')
            ->once();

        $commandTester->setInputs([1, 'y']);
        $commandTester->execute([
            'command' => $command->getName()
        ]);

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
    public function it_should_notify_user_when_all_languages_are_deleted()
    {
        $command = $this->application->find('sf:delete-language');
        $commandTester = new CommandTester($command);

        $fields = $this->givenAnArrayOfLanguages();
        unset($fields[1]);

        $this->languageManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);

        $this->languageManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($fields[0]);

        $this->languageManager
            ->shouldReceive('delete')
            ->once();

        $this->languageManager
            ->shouldReceive('readAll')
            ->once()
            ->andThrow(LanguageNotFoundException::class);

        $commandTester->setInputs([1, 'y']);
        $commandTester->execute([
            'command' => $command->getName()
        ]);

        $this->assertRegExp(
            '/No languages left/',
            $commandTester->getDisplay()
        );
    }


    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_not_delete_language_when_user_does_not_confirm()
    {
        $command = $this->application->find('sf:delete-language');
        $commandTester = new CommandTester($command);
        $fields = $this->givenAnArrayOfLanguages();
        $this->languageManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);
        $this->languageManager
            ->shouldReceive('read')
            ->once()
            ->andReturn($fields[0]);
        $this->languageManager
            ->shouldNotReceive('delete');
        $commandTester->setInputs([1, 'n']);
        $commandTester->execute(['command' => $command->getName()]);
        $this->assertRegExp(
            '/Cancelled/',
            $commandTester->getDisplay()
        );
    }


    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_not_try_to_delete_non_existing_languages()
    {
        $command = $this->application->find('sf:delete-language');
        $commandTester = new CommandTester($command);
        $fields = $this->givenAnArrayOfLanguages();
        $this->languageManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($fields);
        $this->languageManager
            ->shouldReceive('read')
            ->andThrow(LanguageNotFoundException::class);
        $commandTester->setInputs([9]);
        $commandTester->execute(['command' => $command->getName()]);
        $this->assertRegExp(
            '/Language not found/',
            $commandTester->getDisplay()
        );
    }
}
