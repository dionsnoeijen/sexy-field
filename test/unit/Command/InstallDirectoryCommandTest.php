<?php
declare(strict_types=1);

namespace Tardigrades\Command;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tardigrades\SectionField\Service\ApplicationManagerInterface;
use Tardigrades\SectionField\Service\ApplicationNotFoundException;
use Tardigrades\SectionField\Service\FieldManagerInterface;
use Tardigrades\SectionField\Service\FieldNotFoundException;
use Tardigrades\SectionField\Service\FieldTypeManagerInterface;
use Tardigrades\SectionField\Service\FieldTypeNotFoundException;
use Tardigrades\SectionField\Service\LanguageManagerInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\Service\SectionNotFoundException;

/**
 * @coversDefaultClass Tardigrades\Command\InstallDirectoryCommand
 * @covers ::__construct
 */
final class InstallDirectoryCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var Mock|ApplicationManagerInterface */
    private $applicationManager;

    /** @var Mock|LanguageManagerInterface */
    private $languageManager;

    /** @var Mock|SectionManagerInterface */
    private $sectionManager;

    /** @var Mock|FieldManagerInterface */
    private $fieldManager;

    /** @var Mock|FieldTypeManagerInterface */
    private $fieldTypeManager;

    /** @var InstallDirectoryCommand */
    private $installDirectoryCommand;

    private $application;

    public function setUp(): void
    {
        $this->applicationManager = \Mockery::mock(ApplicationManagerInterface::class);
        $this->languageManager = \Mockery::mock(LanguageManagerInterface::class);
        $this->sectionManager = \Mockery::mock(SectionManagerInterface::class);
        $this->fieldManager = \Mockery::mock(FieldManagerInterface::class);
        $this->fieldTypeManager = \Mockery::mock(FieldTypeManagerInterface::class);

        $this->installDirectoryCommand = new InstallDirectoryCommand(
            $this->applicationManager,
            $this->languageManager,
            $this->sectionManager,
            $this->fieldManager,
            $this->fieldTypeManager
        );

        $this->application = new Application();
        $this->application->add($this->installDirectoryCommand);
    }

    /**
     * @test
     * @covers ::<private>
     * @covers ::<protected>
     */
    public function it_installs_valid_config_correctly()
    {
        $this->fieldTypeManager->shouldReceive('readByType')
            ->twice()
            ->andThrow(FieldTypeNotFoundException::class);
        $this->fieldTypeManager->shouldReceive('createWithFullyQualifiedClassName')->twice();

        $this->languageManager->shouldReceive('createByConfig')->once();

        $this->applicationManager->shouldReceive('readByHandle')
            ->once()
            ->andThrow(ApplicationNotFoundException::class);
        $this->applicationManager->shouldReceive('createByConfig')->once();

        $this->fieldManager->shouldReceive('readByHandle')
            ->twice()
            ->andThrow(FieldNotFoundException::class);
        $this->fieldManager->shouldReceive('createByConfig')->twice();

        $this->sectionManager->shouldReceive('readByHandle')
            ->once()
            ->andThrow(SectionNotFoundException::class);
        $this->sectionManager->shouldReceive('createByConfig')->once();

        $commandTester = $this->runWithFilesystem($this->setupFilesystem());

        $expectedOutput = <<<EOF
Languages created!
1 applications created!
2 field types installed!
2 fields created!
1 sections created!

EOF;

        $this->assertSame($expectedOutput, $commandTester->getDisplay());
    }

    /**
     * @test
     * @covers ::<private>
     * @covers ::<protected>
     */
    public function it_updates_valid_config_correctly()
    {
        $this->fieldTypeManager->shouldReceive('readByType')
            ->twice();
        $this->fieldTypeManager->shouldReceive('createWithFullyQualifiedClassName')
            ->never();

        $this->languageManager->shouldReceive('createByConfig')->once();

        $this->applicationManager->shouldReceive('readByHandle')
            ->once();
        $this->applicationManager->shouldReceive('updateByConfig')->once();

        $this->fieldManager->shouldReceive('readByHandle')
            ->twice()
            ->andThrow(FieldNotFoundException::class);
        $this->fieldManager->shouldReceive('createByConfig')->twice();

        $this->sectionManager->shouldReceive('readByHandle')
            ->once()
            ->andThrow(SectionNotFoundException::class);
        $this->sectionManager->shouldReceive('createByConfig')->once();

        $commandTester = $this->runWithFilesystem($this->setupFilesystem());

        $expectedOutput = <<<EOF
Languages created!
1 applications created!
2 field types installed!
2 fields created!
1 sections created!

EOF;

        $this->assertSame($expectedOutput, $commandTester->getDisplay());
    }

    /**
     * @test
     * @covers ::verifyConfig
     */
    public function it_fails_without_an_application()
    {
        $this->expectException(\Exception::class);

        $fileSystem = $this->setupFilesystem();
        $fileSystem->removeChild('application.yml');

        $this->runWithFilesystem($fileSystem);
    }

    /**
     * @test
     * @covers ::verifyConfig
     */
    public function it_fails_without_languages()
    {
        $this->expectException(\Exception::class);

        $fileSystem = $this->setupFilesystem();
        $fileSystem->removeChild('language.yml');

        $this->runWithFilesystem($fileSystem);
    }

    /**
     * @test
     * @covers ::classifyFile
     */
    public function it_fails_with_multiple_language_files()
    {
        $this->expectException(\Exception::class);

        $fileSystem = $this->setupFilesystem();
        $extraFile = new vfsStreamFile("extra.yml");
        $extraFile->setContent("language: ~");
        $fileSystem->addChild($extraFile);

        $this->runWithFilesystem($fileSystem);
    }

    /**
     * @test
     * @covers ::classifyFile
     */
    public function it_fails_with_a_non_array_file()
    {
        $this->expectException(\Exception::class);

        $fileSystem = $this->setupFilesystem();
        $extraFile = new vfsStreamFile("extra.yml");
        $extraFile->setContent("~");
        $fileSystem->addChild($extraFile);

        $this->runWithFilesystem($fileSystem);
    }

    /**
     * @test
     * @covers ::classifyFile
     */
    public function it_fails_with_a_file_with_multiple_keys()
    {
        $this->expectException(\Exception::class);

        $fileSystem = $this->setupFilesystem();
        $extraFile = new vfsStreamFile("extra.yml");
        $extraFile->setContent("language: ~\nfoo: ~");
        $fileSystem->addChild($extraFile);

        $this->runWithFilesystem($fileSystem);
    }

    /**
     * @test
     * @covers ::classifyFile
     */
    public function it_fails_with_a_file_with_an_unknown_type()
    {
        $this->expectException(\Exception::class);

        $fileSystem = $this->setupFilesystem();
        $extraFile = new vfsStreamFile("extra.yml");
        $extraFile->setContent("foo: ~");
        $fileSystem->addChild($extraFile);

        $this->runWithFilesystem($fileSystem);
    }

    private function runWithFilesystem(vfsStreamDirectory $fileSystem): CommandTester
    {
        $commandTester = new CommandTester($this->installDirectoryCommand);
        $commandTester->execute(
            [
                'command' => $this->installDirectoryCommand->getName(),
                'directory' => $fileSystem->url()
            ]
        );
        return $commandTester;
    }

    private function setupFilesystem(): vfsStreamDirectory
    {
        $application = <<<EOF
application:
    name: MyApplication
    handle: AppHandle
    languages:
        - nl_NL
        - en_EN
EOF;

        $language = <<<EOF
language:
    - nl_NL
    - en_EN
EOF;

        $sectionFoo = <<<EOF
section:
    name: Foo
    handle: foo
    fields:
        - foofield
        - fieldfoo
    default:
        - foofield
    namespace: Somewhere
EOF;

        $fieldFoo = <<<EOF
field:
    name: Field foo
    handle: fieldfoo
    type: TextInput
EOF;

        $fooField = <<<EOF
field:
    name: Foo field
    handle: foofield
    type: DateTimeField
EOF;


        return vfsStream::setup('config', 444, [
            'application.yml' => $application,
            'language.yml' => $language,
            'foo' => [
                'foo.yml' => $sectionFoo,
                'fields' => [
                    'fieldfoo.yml' => $fieldFoo,
                    'foofield.yml' => $fooField
                ]
            ],
            'README' => "I am not a yml, please ignore me"
        ]);
    }
}
