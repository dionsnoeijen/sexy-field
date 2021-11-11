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
use Symfony\Component\Yaml\Yaml;
use Tardigrades\SectionField\Service\ApplicationManagerInterface;
use Tardigrades\SectionField\ValueObject\ApplicationConfig;

class CreateApplicationCommand extends ApplicationCommand
{
    private ApplicationManagerInterface $applicationManager;

    public function __construct(
        ApplicationManagerInterface $applicationManager
    ) {
        $this->applicationManager = $applicationManager;

        parent::__construct('sf:create-application');
    }

    protected function configure()
    {
        // @codingStandardsIgnoreStart
        $this
            ->setDescription('Create application')
            ->setHelp('Create a new application, an application is related to sections, you can group sections and isolate them when necessary. You need at least one application, and only one application counts as default.')
            ->addArgument('config', InputArgument::REQUIRED, 'The application configuration yml');
        // @codingStandardsIgnoreEnd
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $input->getArgument('config');

        try {
            $applicationConfig = ApplicationConfig::fromArray(
                Yaml::parse(file_get_contents($config))
            );
            $this->applicationManager->createByConfig($applicationConfig);
            $output->writeln('<info>Application created!</info>');
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid config. {$exception->getMessage()}</error>");
            return 1;
        }

        return 0;
    }
}
