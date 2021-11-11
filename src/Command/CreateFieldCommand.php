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

use Mockery\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Tardigrades\SectionField\Service\FieldManagerInterface;
use Tardigrades\SectionField\Service\FieldNotFoundException;
use Tardigrades\SectionField\ValueObject\FieldConfig;

class CreateFieldCommand extends FieldCommand
{
    /** @var FieldManagerInterface */
    private $fieldManager;

    public function __construct(
        FieldManagerInterface $fieldManager
    ) {
        $this->fieldManager = $fieldManager;

        parent::__construct('sf:create-field');
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a field.')
            ->setHelp('Create field based on a config file.')
            ->addArgument('config', InputArgument::REQUIRED, 'The field configuration yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $input->getArgument('config');

        try {
            if (file_exists($config)) {
                $parsed = Yaml::parse(file_get_contents($config));
                if (is_array($parsed)) {
                    $fieldConfig = FieldConfig::fromArray($parsed);
                    try {
                        $this->fieldManager->readByHandle($fieldConfig->getHandle());
                        $output->writeln('<info>This field already exists</info>');
                    } catch (FieldNotFoundException $exception) {
                        $this->fieldManager->createByConfig($fieldConfig);
                        $output->writeln('<info>Field created!</info>');
                    }
                    return 0;
                }
            }
            throw new Exception('No valid config found.');
        } catch (\Exception $exception) {
            $output->writeln("<error>Invalid field config. {$exception->getMessage()}</error>");
            return 1;
        }
    }
}
