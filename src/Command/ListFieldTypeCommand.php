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
use Tardigrades\SectionField\Service\FieldTypeManagerInterface;
use Tardigrades\SectionField\Service\FieldTypeNotFoundException;

class ListFieldTypeCommand extends FieldTypeCommand
{
    private FieldTypeManagerInterface $fieldTypeManager;

    public function __construct(
        FieldTypeManagerInterface $fieldTypeManager
    ) {
        $this->fieldTypeManager = $fieldTypeManager;

        parent::__construct('sf:list-field-type');
    }

    protected function configure()
    {
        $this
            ->setDescription('Show installed field types.')
            ->setHelp('This command lists all installed field types.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $fieldTypes = $this->fieldTypeManager->readAll();
            $this->renderTable($output, $fieldTypes, 'All installed FieldTypes');
            return 0;
        } catch (FieldTypeNotFoundException $exception) {
            $output->writeln('No FieldType found');
            return 1;
        }
    }
}
