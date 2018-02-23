<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\Service\SectionNotFoundException;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class UpdateSectionCommand extends SectionCommand
{
    /** @var QuestionHelper */
    private $questionHelper;

    public function __construct(
        SectionManagerInterface $sectionManager
    ) {
        parent::__construct($sectionManager, 'sf:update-section');
    }

    protected function configure(): void
    {
        // @codingStandardsIgnoreStart
        $this
            ->setDescription('Updates an existing section.')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command allows you to update a section based on a yml section cofiguration. Pass along the path to a section cofiguration yml. Something like: section/blog.yml

You can automatically continue with flags:

  <info>--yes-mode --in-history</info>

Will store the section config that will be replaced in history.

  <info>--yes-mode --not-in-history</info>

Will not store anything in history.

EOF
            )
            ->addArgument('config', InputArgument::REQUIRED, 'The section configuration yml')
            ->addOption(
                'yes-mode',
                'ym',
                InputOption::VALUE_NONE,
                'Automatically say yes when a field handle is found'
            )
            ->addOption(
                'in-history',
                null,
                InputOption::VALUE_NONE,
                'Set this flag if you want to store the section in history automatically'
            )
            ->addOption(
                'not-in-history',
                null,
                InputOption::VALUE_NONE,
                'Set this flag if you don\'t want to store the section in history automatically'
            )
        ;
        // @codingStandardsIgnoreEnd
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->questionHelper = $this->getHelper('question');
            $this->showInstalledSections($input, $output);
        } catch (SectionNotFoundException $exception) {
            $output->writeln("Section not found");
        }
    }

    private function showInstalledSections(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getOption('yes-mode')) {
            $this->renderTable($output, $this->sectionManager->readAll(), 'All installed Sections');
        }
        $this->updateWhatRecord($input, $output);
    }

    private function getConfig(InputInterface $input, OutputInterface $output): ?SectionConfig
    {
        try {
            $config = $input->getArgument('config');
            $sectionConfig = SectionConfig::fromArray(
                Yaml::parse(
                    file_get_contents($config)
                )
            );
            return $sectionConfig;
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid configuration file.  {$exception->getMessage()}</error>");
            return null;
        }
    }

    private function updateWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        if (is_null($sectionConfig = $this->getConfig($input, $output))) {
            return;
        }

        try {
            $section = $this->sectionManager->readByHandle($sectionConfig->getHandle());
            if (!$input->getOption('yes-mode')) {
                $sure = new ConfirmationQuestion(
                    '<comment>Do you want to update the section with id: ' . $section->getId() . '?</comment> (y/n) ',
                    false
                );

                if (!$this->questionHelper->ask($input, $output, $sure)) {
                    $output->writeln('<comment>Cancelled, nothing updated.</comment>', false);
                    return;
                }
            }

        } catch (SectionNotFoundException $exception) {
            $output->writeln(
                'You are trying to update a section with handle: ' . $sectionConfig->getHandle() . '. No field with ' .
                'that handle exists in the database, use sf:create-section is you actually need a new section, or' .
                'select an existing section id that will be overwritten with this config.'
            );

            $sure = new ConfirmationQuestion(
                '<comment>Do you want to continue to select a section that will be overwritten?</comment> (y/n) ', false
            );

            if (!$this->getHelper('question')->ask($input, $output, $sure)) {
                $output->writeln('<comment>Cancelled, nothing updated</comment>');
                return;
            }

            $section = $this->getSection($input, $output);
        }

        $inHistory = $input->getOption('in-history');
        $notInHistory = $input->getOption('not-in-history');

        if (!$inHistory && !$notInHistory) {
            $inHistory = $this->getHelper('question')->ask(
                $input,
                $output,
                new ConfirmationQuestion(
                    '<comment>Do you want to store the current version in history?</comment> (y/n) ',
                    false
                )
            );
        }

        if ($notInHistory) {
            $inHistory = false;
        }

        $this->sectionManager->updateByConfig($sectionConfig, $section, $inHistory);
        if (!$input->getOption('yes-mode')) {
            $this->renderTable($output, $this->sectionManager->readAll(), 'Section updated!');
        } else {
            $output->writeln(
                'Section updated! ' .
                ($inHistory ? 'Old version stored in history.' : 'Nothing stored in history.')
            );
        }
    }
}
