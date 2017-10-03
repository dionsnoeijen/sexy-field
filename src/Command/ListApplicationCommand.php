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
use Tardigrades\SectionField\Service\ApplicationManagerInterface;
use Tardigrades\SectionField\Service\ApplicationNotFoundException;

class ListApplicationCommand extends ApplicationCommand
{
    private $applicationManager;

    public function __construct(
        ApplicationManagerInterface $applicationManager
    ) {
        $this->applicationManager = $applicationManager;

        parent::__construct('sf:list-application');
    }

    protected function configure()
    {
        $this
            ->setDescription('Show applications')
            ->setHelp('Show applications');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $applications = $this->applicationManager->readAll();
            $this->renderTable($output, $applications, 'All installed Applications');
        } catch (ApplicationNotFoundException $exception) {
            $output->writeln('No applications found');
        }
    }
}
