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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\Field;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tardigrades\SectionField\Service\FieldManagerInterface;
use Tardigrades\SectionField\Service\FieldNotFoundException;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Id;

class UpdateFieldCommand extends FieldCommand
{
    private QuestionHelper $questionHelper;

    private FieldManagerInterface $fieldManager;

    public function __construct(
        FieldManagerInterface $fieldManager
    ) {
        $this->fieldManager = $fieldManager;

        parent::__construct('sf:update-field');
    }

    protected function configure()
    {
        $this
            ->setDescription('Updates an existing field.')
            ->setHelp('Update field by giving a new or updated field config file.')
            ->addArgument('config', InputArgument::REQUIRED, 'The field configuration yml')
            ->addOption(
                'yes-mode',
                null,
                InputOption::VALUE_NONE,
                'Automatically say yes when a field handle is found'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->questionHelper = $this->getHelper('question');
            $this->showInstalledFields($input, $output);
        } catch (FieldNotFoundException $exception) {
            $output->writeln("Field not found");
        }
        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws FieldNotFoundException
     */
    private function showInstalledFields(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getOption('yes-mode')) {
            $this->renderTable($output, $this->fieldManager->readAll(), 'All installed Fields');
        }
        $this->updateWhatRecord($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Field
     * @throws FieldNotFoundException
     */
    private function getField(InputInterface $input, OutputInterface $output): Field
    {
        $question = new Question('<question>What record do you want to update?</question> (#id): ');
        $question->setValidator(function ($id) {
            try {
                return $this->fieldManager->read(Id::fromInt((int) $id));
            } catch (FieldNotFoundException $exception) {
                return null;
            }
        });

        $field = $this->questionHelper->ask($input, $output, $question);
        if (!$field) {
            throw new FieldNotFoundException();
        }
        return $field;
    }

    private function updateWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        $config = $input->getArgument('config');

        try {
            $fieldConfig = FieldConfig::fromArray(
                Yaml::parse(
                    file_get_contents($config)
                )
            );
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid configuration file.  {$exception->getMessage()}</error>");
            return;
        }

        try {
            $field = $this->fieldManager->readByHandle($fieldConfig->getHandle());

            if (!$input->getOption('yes-mode')) {
                $sure = new ConfirmationQuestion(
                    '<comment>Do you want to update the field with id: ' . $field->getId() . '?</comment> (y/n) ',
                    false
                );

                if (!$this->getHelper('question')->ask($input, $output, $sure)) {
                    $output->writeln('<comment>Cancelled, nothing updated.</comment>', false);
                    return;
                }
            }
        } catch (FieldNotFoundException $exception) {
            $output->writeln(
                'You are trying to update a field with handle: ' . $fieldConfig->getHandle() . '. No field with ' .
                'that handle exists in the database, use sf:create-field if you actually need a new field, or ' .
                'select an existing field id that will be overwritten with this config.'
            );

            $sure = new ConfirmationQuestion(
                '<comment>Do you want to continue to select a field that will be overwritten?</comment> (y/n) ',
                false
            );

            if (!$this->getHelper('question')->ask($input, $output, $sure)) {
                $output->writeln('<comment>Cancelled, nothing updated.</comment>');
                return;
            }

            $field = $this->getField($input, $output);
        }

        $this->fieldManager->updateByConfig($fieldConfig, $field);
        if (!$input->getOption('yes-mode')) {
            $this->renderTable($output, $this->fieldManager->readAll(), 'Field updated!');
        } else {
            $output->writeln('Field updated!');
        }
    }
}
