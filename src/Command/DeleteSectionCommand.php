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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\Service\SectionNotFoundException;

class DeleteSectionCommand extends SectionCommand
{
    public function __construct(
        SectionManagerInterface $sectionManager
    ) {
        parent::__construct($sectionManager, 'sf:delete-section');
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete section.')
            ->setHelp('Delete section.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $sections = $this->sectionManager->readAll();
            $this->renderTable($output, $sections, 'All installed Sections');
            $this->deleteWhatRecord($input, $output);
        } catch (SectionNotFoundException $exception) {
            $output->writeln("Section not found.");
        }

        return 0;
    }

    private function deleteWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        /** @var SectionInterface $section */
        $sections = $this->getSections($input, $output);

        foreach ($sections as $section) {
            $output->writeln('<info>Record with id #' . $section->getId() . ' will be deleted</info>');

            $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);

            if (!$this->getHelper('question')->ask($input, $output, $sure)) {
                $output->writeln('<comment>Cancelled, nothing deleted.</comment>');
                return;
            }

            $this->sectionManager->delete($section);

            $output->writeln('<info>Removed!</info>');
        }
    }
}
