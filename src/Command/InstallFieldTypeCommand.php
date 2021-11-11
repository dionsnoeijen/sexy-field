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
use Tardigrades\SectionField\Service\FieldTypeManagerInterface;
use Tardigrades\SectionField\Service\FieldTypeNotFoundException;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;

class InstallFieldTypeCommand extends FieldTypeCommand
{
    /** @var FieldTypeManagerInterface */
    private $fieldTypeManager;

    public function __construct(
        FieldTypeManagerInterface $fieldTypeManager
    ) {
        $this->fieldTypeManager = $fieldTypeManager;
        parent::__construct('sf:install-field-type');
    }

    protected function configure()
    {
        $this
            ->setDescription('Install a field type. Escape the backslash! Like so: This\\\Is\\\ClassName')
            ->setHelp('This command installs a field type, just give the namespace where to find the field.')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Field type namespace')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $namespace = $input->getArgument('namespace');
        $fullyQualifiedClassName = FullyQualifiedClassName::fromString($namespace);

        try {
            $this->fieldTypeManager->readByFullyQualifiedClassName($fullyQualifiedClassName);
            $output->writeln('<info>FieldType already installed</info>');
            return 1;
        } catch (FieldTypeNotFoundException $exception) {
            $fieldType = $this->fieldTypeManager->createWithFullyQualifiedClassName($fullyQualifiedClassName);
            $this->renderTable($output, [$fieldType], 'FieldTypeInterface installed!');
            return 0;
        }
    }
}
