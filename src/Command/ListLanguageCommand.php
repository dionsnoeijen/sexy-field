<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types=1);

namespace Tardigrades\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\Service\LanguageManagerInterface;
use Tardigrades\SectionField\Service\LanguageNotFoundException;

class ListLanguageCommand extends LanguageCommand
{
    private $languageManager;

    public function __construct(
        LanguageManagerInterface $languageManager
    ) {
        $this->languageManager = $languageManager;

        parent::__construct('sf:list-language');
    }

    protected function configure()
    {
        $this
            ->setDescription('List language')
            ->setHelp('List all installed languages');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $languages = $this->languageManager->readAll();
            $this->renderTable($output, $languages, 'All installed languages');
        } catch (LanguageNotFoundException $exception) {
            $output->writeln('No language found');
        }
    }
}
