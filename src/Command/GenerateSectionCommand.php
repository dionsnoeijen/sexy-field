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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Tardigrades\SectionField\Generator\Writer\GeneratorFileWriter;
use Tardigrades\SectionField\Generator\Writer\Writable;
use Tardigrades\SectionField\Generator\GeneratorsInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\Service\SectionNotFoundException;

class GenerateSectionCommand extends SectionCommand
{
    /** @var GeneratorsInterface */
    private $entityGenerator;

    public function __construct(
        SectionManagerInterface $sectionManager,
        GeneratorsInterface $entityGenerator
    ) {
        $this->entityGenerator = $entityGenerator;

        parent::__construct($sectionManager, 'sf:generate-section');
    }

    protected function configure(): void
    {
        // @codingStandardsIgnoreStart
        $this
            ->setDescription('Generate a section.')
            ->setHelp('After creating a section, you can generate the accompanying files and tables (when using doctrine settings).')
            ->addOption(
                'yes-mode',
                null,
                InputOption::VALUE_NONE,
                'Automatically say yes when a confirmation is asked'
            )
            ->addOption(
                'all',
                'a',
                InputOption::VALUE_NONE,
                'Generate all available sections'
            )
        ;
        // @codingStandardsIgnoreEnd
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $sections = $this->sectionManager->readAll();
            $this->renderTable($output, $sections, 'Available sections.');
            $this->generateWhatSection($input, $output);
            return 0;
        } catch (SectionNotFoundException $exception) {
            $output->writeln("Section not found.");
            return 1;
        }
    }

    private function generateWhatSection(InputInterface $input, OutputInterface $output): void
    {
        if ($input->getOption('all')) {
            $sections = $this->sectionManager->readAll();
        } else {
            $sections = $this->getSections($input, $output);
        }

        foreach ($sections as $section) {
            $writables = $this->entityGenerator->generateBySection($section);

            /** @var Writable $writable */
            foreach ($writables as $writable) {
                $output->writeln(
                    '<info>------------ * TEMPLATE: ' .
                    $writable->getNamespace() . $writable->getFilename() .
                    ' * ------------</info>'
                );
                $output->writeln($writable->getTemplate());
            }

            if (!$input->getOption('yes-mode')) {
                $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);
                if (!$this->getHelper('question')->ask($input, $output, $sure)) {
                    $output->writeln('<comment>Cancelled, nothing generated.</comment>');
                    return;
                }
            }
            foreach ($writables as $writable) {
                GeneratorFileWriter::write($writable);
            }
        }

        $output->writeln($this->entityGenerator->getBuildMessages());
    }
}
