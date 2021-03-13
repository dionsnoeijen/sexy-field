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
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\FieldType;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\Service\FieldNotFoundException;
use Tardigrades\SectionField\Service\FieldTypeManagerInterface;
use Tardigrades\SectionField\Service\FieldTypeNotFoundException;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Id;

class UpdateFieldTypeCommand extends FieldTypeCommand
{
    private QuestionHelper $questionHelper;
    private FieldTypeManagerInterface $fieldTypeManager;

    public function __construct(
        FieldTypeManagerInterface $fieldTypeManager
    ) {
        $this->fieldTypeManager = $fieldTypeManager;

        parent::__construct('sf:update-field-type');
    }

    protected function configure()
    {
        $this
            ->setDescription('Updates a field type.')
            ->setHelp('Update a field type based on new fully qualified class name.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->questionHelper = $this->getHelper('question');
            $this->showInstalledFieldTypes($input, $output);
        } catch (FieldTypeNotFoundException $exception) {
            $output->writeln("Field type not found");
        }
        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws FieldTypeNotFoundException
     */
    private function showInstalledFieldTypes(InputInterface $input, OutputInterface $output): void
    {
        $fieldTypes = $this->fieldTypeManager->readAll();

        $this->renderTable($output, $fieldTypes, 'The * column is what can be updated, type is updated automatically.');
        $this->updateWhatRecord($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return FieldType
     * @throws FieldTypeNotFoundException
     */
    private function getFieldType(InputInterface $input, OutputInterface $output): FieldType
    {
        $question = new Question('<question>What record do you want to update?</question> (#id): ');
        $question->setValidator(function ($id) {
            try {
                return $this->fieldTypeManager->read(Id::fromInt((int) $id));
            } catch (FieldTypeNotFoundException $exception) {
                return null;
            }
        });

        $fieldType = $this->questionHelper->ask($input, $output, $question);
        if (!$fieldType) {
            throw new FieldTypeNotFoundException();
        }
        return $fieldType;
    }

    private function getNamespace(
        InputInterface $input,
        OutputInterface $output,
        FieldType $fieldType
    ): FullyQualifiedClassName {
        $updateQuestion = new Question(
            '<question>Give a new fully qualified class name</question> (old: ' .
            $fieldType->getFullyQualifiedClassName() .
            '): '
        );
        $updateQuestion->setValidator(function ($fullyQualifiedClassName) {
            return FullyQualifiedClassName::fromString($fullyQualifiedClassName);
        });

        return $this->questionHelper->ask($input, $output, $updateQuestion);
    }

    private function updateWhatRecord(InputInterface $input, OutputInterface $output)
    {
        $fieldType = $this->getFieldType($input, $output);
        $namespace = $this->getNamespace($input, $output, $fieldType);

        $output->writeln(
            '<info>Record with id #' .
            $fieldType->getId() .
            ' will be updated with namespace: </info>' .
            (string) $namespace
        );

        $sure = new ConfirmationQuestion('<comment>Are you sure?</comment> (y/n) ', false);

        if (!$this->questionHelper->ask($input, $output, $sure)) {
            $output->writeln('<comment>Cancelled, nothing updated.</comment>');
            return;
        }

        $this->updateRecord($input, $output, $fieldType, $namespace);
    }

    private function updateRecord(
        InputInterface $input,
        OutputInterface $output,
        FieldType $fieldType,
        FullyQualifiedClassName $fullyQualifiedClassName
    ) {
        $fieldType->setType($fullyQualifiedClassName->getClassName());
        $fieldType->setFullyQualifiedClassName((string) $fullyQualifiedClassName);
        $this->fieldTypeManager->update();
        $this->renderTable(
            $output,
            [$fieldType],
            'The * column is what can be updated, type is updated automatically.'
        );

        $output->writeln('<info>FieldTypeInterface Updated!</info>');
    }
}
