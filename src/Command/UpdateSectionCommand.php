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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\Service\SectionNotFoundException;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class UpdateSectionCommand extends SectionCommand
{
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
            ->setHelp('This command allows you to update a section based on a yml section configuration. Pass along the path to a section configuration yml. Something like: section/blog.yml')
            ->addArgument('config', InputArgument::REQUIRED, 'The section configuration yml')
        ;
        // @codingStandardsIgnoreEnd
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $sections = $this->sectionManager->readAll();
            $this->renderTable($output, $sections, 'All installed Sections');
            $this->updateWhatRecord($input, $output);
        } catch (SectionNotFoundException $exception) {
            $output->writeln("Section not found.");
        }
    }

    private function updateWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        $section = $this->getSection($input, $output);
        $config = $input->getArgument('config');

        try {
            $sectionConfig = SectionConfig::fromArray(
                Yaml::parse(
                    file_get_contents($config)
                )
            );

            $inHistory = $this->getHelper('question')->ask(
                $input,
                $output,
                new ConfirmationQuestion(
                    '<comment>Do you want to store the current version in history?</comment> (y/n) ',
                    false
                )
            );

            $this->sectionManager->updateByConfig($sectionConfig, $section, $inHistory);
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid configuration file.  {$exception->getMessage()}</error>");
            return;
        }

        $sections = $this->sectionManager->readAll();
        $this->renderTable($output, $sections, 'Section updated!');
    }
}
