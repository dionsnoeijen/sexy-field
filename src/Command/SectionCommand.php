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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\Service\SectionNotFoundException;
use Tardigrades\SectionField\ValueObject\Id;

abstract class SectionCommand extends Command
{
    const ALL = 'all';

    /** @var SectionManagerInterface */
    protected $sectionManager;

    public function __construct(
        SectionManagerInterface $sectionManager,
        string $name
    ) {
        $this->sectionManager = $sectionManager;

        parent::__construct($name);
    }

    protected function renderTable(OutputInterface $output, array $sections, string $info)
    {
        $table = new Table($output);

        $rows = [];
        /** @var SectionInterface $section */
        foreach ($sections as $section) {
            $rows[] = [
                $section->getId(),
                $section->getName(),
                $section->getHandle(),
                (string) $section->getConfig(),
                $section->getUpdated()->format('D-m-y'),
                $section->getVersion()
            ];
        }

        $rows[] = new TableSeparator();
        $rows[] = [
            new TableCell('<info>' . $info . '</info>', ['colspan' => 5])
        ];

        $table
            ->setHeaders(['#id', 'name', 'handle', 'config', 'updated', 'version'])
            ->setRows($rows)
        ;
        $table->render();
    }

    protected function getSection(InputInterface $input, OutputInterface $output): ?SectionInterface
    {
        $question = new Question('<question>Choose record.</question> (#id): ');
        $question->setValidator(function ($id) {
            try {
                return $this->sectionManager->read(Id::fromInt((int) $id));
            } catch (SectionNotFoundException $exception) {
                // Exceptions thrown from here seemingly can't be caught, so signal with a return value instead
                return null;
            }
        });
        $section = $this->getHelper('question')->ask($input, $output, $question);
        if (!$section) {
            throw new SectionNotFoundException();
        }
        return $section;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return SectionInterface[]
     * @throws SectionNotFoundException
     */
    protected function getSections(InputInterface $input, OutputInterface $output): array
    {
        $question = new Question('<question>Choose record.</question> (#id): ');
        $question->setValidator(function ($id) {
            try {
                if ($id === self::ALL) {
                    return $this->sectionManager->readAll();
                }

                if (strpos($id, ',') !== false) {
                    $ids = explode(',', $id);
                    return $this->sectionManager->readByIds($ids);
                }

                return [$this->sectionManager->read(Id::fromInt((int) $id))];
            } catch (SectionNotFoundException $exception) {
                // Exceptions thrown from here seemingly can't be caught, so signal with a return value instead
                return null;
            }
        });

        $section = $this->getHelper('question')->ask($input, $output, $question);
        if (!$section) {
            throw new SectionNotFoundException();
        }
        return $section;
    }
}
