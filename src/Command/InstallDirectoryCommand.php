<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\SectionField\Service\ApplicationManagerInterface;
use Tardigrades\SectionField\Service\FieldManagerInterface;
use Tardigrades\SectionField\Service\FieldTypeManagerInterface;
use Tardigrades\SectionField\Service\FieldTypeNotFoundException;
use Tardigrades\SectionField\Service\LanguageManagerInterface;
use Tardigrades\SectionField\Service\NotFoundException;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\ApplicationConfig;
use Tardigrades\SectionField\ValueObject\ConfigWithHandleInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\LanguageConfig;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\Type;

class InstallDirectoryCommand extends Command
{
    /** @var ApplicationConfig[] */
    private $applications = [];

    /** @var ?LanguageConfig */
    private $languages = null;

    /** @var SectionConfig[] */
    private $sections = [];

    /** @var FieldConfig[] */
    private $fields = [];

    /** @var ApplicationManagerInterface */
    private $applicationManager;

    /** @var LanguageManagerInterface */
    private $languageManager;

    /** @var SectionManagerInterface */
    private $sectionManager;

    /** @var FieldManagerInterface */
    private $fieldManager;

    /** @var FieldTypeManagerInterface */
    private $fieldTypeManager;

    public function __construct(
        ApplicationManagerInterface $applicationManager,
        LanguageManagerInterface $languageManager,
        SectionManagerInterface $sectionManager,
        FieldManagerInterface $fieldManager,
        FieldTypeManagerInterface $fieldTypeManager
    ) {
        parent::__construct('sf:install-directory');
        $this->applicationManager = $applicationManager;
        $this->languageManager = $languageManager;
        $this->sectionManager = $sectionManager;
        $this->fieldManager = $fieldManager;
        $this->fieldTypeManager = $fieldTypeManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Install all configuration in a directory')
            ->setHelp('This command installs all .yml files in a directory, recursively. Pass it the path to' .
                'the directory, for example "app/config/my-sexy-field-config".')
            ->addArgument('directory', InputArgument::REQUIRED, 'The config directory');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        foreach (static::getAllYamls($input->getArgument('directory')) as $fileName => $config) {
            $this->classifyFile($fileName, $config);
        }

        $this->verifyConfig();

        $this->createLanguages($this->languages);
        $output->writeln("<info>Languages created!</info>");

        $applicationCount = $this->createApplications($this->applications);
        $output->writeln("<info>$applicationCount applications created!</info>");

        $fieldTypeCount = $this->installFieldTypes($this->fields);
        $output->writeln("<info>$fieldTypeCount field types installed!</info>");

        $fieldCount = $this->createFields($this->fields);
        $output->writeln("<info>$fieldCount fields created!</info>");

        $sectionCount = $this->createSections($this->sections);
        $output->writeln("<info>$sectionCount sections created!</info>");
    }

    /**
     * @throws \Exception
     */
    private function verifyConfig(): void
    {
        if (count($this->applications) === 0) {
            throw new \Exception("Could not find any application config files");
        }
        if (is_null($this->languages)) {
            throw new \Exception("Could not find a language config file");
        }
    }

    /**
     * @param FieldConfig[] $fields
     * @return int the number of field types detected and installed
     */
    private function installFieldTypes(array $fields): int
    {
        $fieldTypes = [];
        foreach ($fields as $field) {
            $fieldType = $field->toArray()['field']['type'];
            if (!in_array($fieldType, $fieldTypes)) {
                $fieldTypes[] = $fieldType;
            }
        }
        // All custom field types are public services, therefore we can find its namespace through its classname.
        $debugString =  `bin/console debug:container`;
        foreach ($fieldTypes as $fieldType) {
            try {
                $this->fieldTypeManager->readByType(Type::fromString($fieldType));
                // Already installed, so continue
                // There's no clean way of updating field types
            } catch (FieldTypeNotFoundException $exception) {
                // Not installed, so install
                if ($fieldType === 'DateTimeField') {
                    // DateTime has "Field" at the end of its name to avoid confusion with \DateTime.
                    // All other field types follow Tardigrades\FieldType\{fieldType}\{fieldType}.
                    // This solution is hacky, but there's no good way to detect classes before they've been loaded.
                    $className = "Tardigrades\\FieldType\\DateTime\\$fieldType";
                } else {
                    // 2 steps to prepare the data. First all the characters between words are reduced to ' ',
                    // then all the words are extracted to an array.
                    $workingString = trim(preg_replace('!\s+!', ' ', $debugString));
                    $arrayOfWords = explode(' ', $workingString);
                    foreach ($arrayOfWords as $word) {
                        if (strpos($word, $fieldType) !== false) {
                            // We still need to make sure it's an exact match.
                            $items = explode('\\', $word);
                            foreach ($items as $item) {
                                if ($item === $fieldType) {
                                    $className = $word;
                                    break;
                                }
                            }
                        } else {
                            $className = "Tardigrades\\FieldType\\$fieldType\\$fieldType";
                        }
                    }
                }
                $this->fieldTypeManager->createWithFullyQualifiedClassName(
                    FullyQualifiedClassName::fromString($className)
                );
            }
        }

        return count($fieldTypes);
    }

    /**
     * @param SectionConfig[] $sections
     * @return int the number of sections created
     */
    private function createSections(array $sections): int
    {
        return $this->createOrUpdateConfigs($sections, $this->sectionManager);
    }

    /**
     * @param FieldConfig[] $fields
     * @return int the number of fields created
     */
    private function createFields(array $fields): int
    {
        return $this->createOrUpdateConfigs($fields, $this->fieldManager);
    }

    /**
     * @param ApplicationConfig[] $applications
     * @return int the number of applications created
     */
    private function createApplications(array $applications): int
    {
        return $this->createOrUpdateConfigs($applications, $this->applicationManager);
    }

    /**
     * @param ConfigWithHandleInterface[] $configs
     * @param ApplicationManagerInterface|FieldManagerInterface|SectionManagerInterface $manager
     * @return int
     */
    private function createOrUpdateConfigs(array $configs, $manager): int
    {
        foreach ($configs as $config) {
            try {
                $handle = $config->getHandle();
                $object = $manager->readByHandle($handle);
                $manager->updateByConfig($config, $object);
            } catch (NotFoundException $exception) {
                $manager->createByConfig($config);
            }
        }
        return count($configs);
    }

    /**
     * @param LanguageConfig $languages
     */
    private function createLanguages(LanguageConfig $languages): void
    {
        $this->languageManager->createByConfig($languages);
    }

    /**
     * @param string $fileName
     * @param mixed $config
     * @throws \Exception
     */
    private function classifyFile(string $fileName, $config): void
    {
        if (!is_array($config)) {
            throw new \Exception("Malformed file $fileName");
        }
        $keys = array_keys($config);
        if (count($keys) !== 1) {
            throw new \Exception("Malformed file $fileName");
        }
        [$key] = $keys;
        switch ($key) {
            case 'field':
                $this->fields[] = FieldConfig::fromArray($config);
                break;
            case 'section':
                $this->sections[] = SectionConfig::fromArray($config);
                break;
            case 'application':
                $this->applications[] = ApplicationConfig::fromArray($config);
                break;
            case 'language':
                if (!is_nulL($this->languages)) {
                    throw new \Exception("Found multiple language config files");
                }
                $this->languages = LanguageConfig::fromArray($config);
                break;
            default:
                throw new \Exception("Could not identify file $fileName with key $key");
        }
    }

    /**
     * Get the contents of all yaml files in a directory, recursively.
     * @param string $directory
     * @return \Generator
     */
    private static function getAllYamls(string $directory): \Generator
    {
        /** @var \SplFileInfo $file */
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
            if ($file->isFile() && $file->getExtension() === 'yml') {
                $fileName = $file->getPathname();
                yield $fileName => Yaml::parse(file_get_contents($fileName));
            }
        }
    }
}
