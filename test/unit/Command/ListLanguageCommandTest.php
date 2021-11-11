<?php
declare (strict_types=1);

namespace Tardigrades\Command;

use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\Entity\Language;
use Tardigrades\SectionField\Service\LanguageManagerInterface;
use Tardigrades\Entity\Application as ApplicationEntity;
use Tardigrades\SectionField\Service\LanguageNotFoundException;

/**
 * @coversDefaultClass Tardigrades\Command\ListLanguageCommand
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class ListLanguageCommandTest  extends TestCase
{
    /** @var LanguageManagerInterface */
    private $languageManager;

    /** @var ListLanguageCommand */
    private $listLanguageCommand;

    /** @var Application */
    private $application;

    public function setUp(): void
    {
        $this->languageManager = Mockery::mock(LanguageManagerInterface::class);
        $this->listLanguageCommand = new ListLanguageCommand($this->languageManager);
        $this->application = new Application();
        $this->application->add($this->listLanguageCommand);
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
    public function it_should_list_languages()
    {
        $command = $this->application->find('sf:list-language');
        $commandTester = new CommandTester($command);

        $languages = $this->givenAnArrayOfLanguages();

        $this->languageManager
            ->shouldReceive('readAll')
            ->once()
            ->andReturn($languages);

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression(
            '/All installed languages/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/nl_NL/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/en_EN/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/Application name/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/Another application name/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/Again, a name/',
            $commandTester->getDisplay()
        );

        $this->assertMatchesRegularExpression(
            '/Fffff, name/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     * @covers ::configure
     * @covers ::execute
     */
    public function it_should_fail_without_languages()
    {
        $command = $this->application->find('sf:list-language');
        $commandTester = new CommandTester($command);

        $this->languageManager
            ->shouldReceive('readAll')
            ->once()
            ->andThrow(LanguageNotFoundException::class);

        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression(
            '/No language found/',
            $commandTester->getDisplay()
        );
    }
}
