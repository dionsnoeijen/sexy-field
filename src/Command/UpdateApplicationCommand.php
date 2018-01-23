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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\Entity\ApplicationInterface;
use Tardigrades\SectionField\Service\ApplicationManagerInterface;
use Tardigrades\SectionField\Service\ApplicationNotFoundException;
use Tardigrades\SectionField\ValueObject\ApplicationConfig;
use Tardigrades\SectionField\ValueObject\Id;

class UpdateApplicationCommand extends ApplicationCommand
{
    /** @var QuestionHelper */
    private $questionHelper;

    /** @var ApplicationManagerInterface */
    private $applicationManager;

    public function __construct(
        ApplicationManagerInterface $applicationManager
    ) {
        $this->applicationManager = $applicationManager;

        parent::__construct('sf:update-application');
    }

    protected function configure(): void
    {
        // @codingStandardsIgnoreStart
        $this
            ->setDescription('Updates an existing application.')
            ->setHelp('This command allows you to update an application based on a yml application configuration. Pass along the path to a application configuration yml. Something like: application/application.yml')
            ->addArgument('config', InputArgument::REQUIRED, 'The application configuration yml')
        ;
        // @codingStandardsIgnoreEnd
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->questionHelper = $this->getHelper('question');
            $this->showInstalledApplications($input, $output);
        } catch (ApplicationNotFoundException $exception) {
            $output->writeln("Section not found");
        }
    }

    private function showInstalledApplications(InputInterface $input, OutputInterface $output): void
    {
        $applications = $this->applicationManager->readAll();

        $this->renderTable($output, $applications, 'All installed Applications');
        $this->updateWhatRecord($input, $output);
    }

    private function getApplicationRecord(InputInterface $input, OutputInterface $output): ApplicationInterface
    {
        $question = new Question('<question>What record do you want to update?</question> (#id): ');
        $question->setValidator(function ($id) use ($output) {
            try {
                return $this->applicationManager->read(Id::fromInt((int) $id));
            } catch (ApplicationNotFoundException $exception) {
                return null;
            }
        });

        $applicationRecord = $this->questionHelper->ask($input, $output, $question);
        if (!$applicationRecord) {
            throw new ApplicationNotFoundException();
        }
        return $applicationRecord;
    }

    private function updateWhatRecord(InputInterface $input, OutputInterface $output): void
    {
        $application = $this->getApplicationRecord($input, $output);
        $config = $input->getArgument('config');

        try {
            $applicationConfig = ApplicationConfig::fromArray(
                Yaml::parse(
                    file_get_contents($config)
                )
            );
            $this->applicationManager->updateByConfig($applicationConfig, $application);
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid configuration file.  {$exception->getMessage()}</error>");
            return;
        }

        $output->writeln('<info>Application updated!</info>');
    }
}
