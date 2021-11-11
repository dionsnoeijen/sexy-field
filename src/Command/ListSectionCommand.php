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
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\Service\SectionNotFoundException;

class ListSectionCommand extends SectionCommand
{
    public function __construct(
        SectionManagerInterface $sectionManager
    ) {
        parent::__construct($sectionManager, 'sf:list-section');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Show installed sections.')
            ->setHelp('This command lists all installed sections.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $sections = $this->sectionManager->readAll();
            $this->renderTable($output, $sections, 'All installed Sections');
            return 0;
        } catch (SectionNotFoundException $exception) {
            $output->writeln('No section found');
            return 1;
        }
    }
}
