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
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Service\SectionHistoryManagerInterface;
use Tardigrades\SectionField\Service\SectionHistoryNotFoundException;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\Service\SectionNotFoundException;
use Tardigrades\SectionField\ValueObject\Id;

class RestoreSectionCommand extends SectionCommand
{
    private SectionHistoryManagerInterface $sectionHistoryManager;

    public function __construct(
        SectionManagerInterface $sectionManager,
        SectionHistoryManagerInterface $sectionHistoryManager
    ) {
        $this->sectionHistoryManager = $sectionHistoryManager;

        parent::__construct($sectionManager, 'sf:restore-section');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Restore a section from history.')
            ->setHelp('Choose a section from history to move back to the active section position')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $sections = $this->sectionManager->readAll();
            $this->renderTable($output, $sections, 'All installed Sections');
            $this->restoreWhatRecord($input, $output);
        } catch (SectionNotFoundException $exception) {
            $output->writeln('Section not found.');
        } catch (SectionHistoryNotFoundException $exception) {
            $output->writeln('Section history not found.');
        }
        return 0;
    }

    private function restoreWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        $sections = $this->getSections($input, $output);

        foreach ($sections as $section) {
            $output->writeln('<info>Record with id #' . $section->getId() .
                ' will be restored, select a record from history to restore the section with.</info>');

            $history = $section->getHistory()->toArray();

            // no need to show a restore option, if there is nothing to restore
            if (count($history) == 0) {
                $output->writeln('<comment>Skipped, no records can be found in history...</comment>');
                continue;
            }

            $this->renderTable($output, $history, 'Section history');
            $sectionFromHistory = $this->getSectionFromHistory($input, $output);

            $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);
            if (!$this->getHelper('question')->ask($input, $output, $sure)) {
                $output->writeln('<comment>Cancelled, nothing restored.</comment>');
                return;
            }

            $this->sectionManager->restoreFromHistory($sectionFromHistory);

            $output->writeln('<info>Config Restored! Run the generate-section command to finish rollback.</info>');
        }
    }

    protected function getSectionFromHistory(InputInterface $input, OutputInterface $output): SectionInterface
    {
        $question = new Question('<question>Choose record.</question> (#id): ');

        $question->setValidator(function ($id) {
            try {
                return $this->sectionHistoryManager->read(Id::fromInt((int) $id));
            } catch (SectionHistoryNotFoundException $exception) {
                return null;
            }
        });

        $sectionFromHistory = $this->getHelper('question')->ask($input, $output, $question);
        if (!$sectionFromHistory) {
            throw new SectionHistoryNotFoundException();
        }
        return $sectionFromHistory;
    }
}
