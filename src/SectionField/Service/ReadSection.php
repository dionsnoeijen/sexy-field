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

namespace Tardigrades\SectionField\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tardigrades\SectionField\Event\BeforeReadAbortedException;
use Tardigrades\SectionField\Event\ReadAbortedException;
use Tardigrades\SectionField\Event\SectionEntryBeforeRead;
use Tardigrades\SectionField\Event\SectionEntryDataRead;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class ReadSection implements ReadSectionInterface
{
    /** @var ReadSectionInterface[] */
    private $readers;

    /** @var SectionManagerInterface */
    private $sectionManager;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(
        array $readers,
        SectionManagerInterface $sectionManager,
        EventDispatcherInterface $dispatcher
    ) {
        $this->readers = $readers;
        $this->sectionManager = $sectionManager;
        $this->dispatcher = $dispatcher;
    }

    public function readMaybe(OptionsInterface $readOptions, SectionConfig $sectionConfig = null): \ArrayIterator
    {
        $sectionData = new \ArrayIterator();

        $beforeReadEvent = new SectionEntryBeforeRead(
            $sectionData,
            $readOptions,
            $sectionConfig
        );
        $this->dispatcher->dispatch($beforeReadEvent);
        if ($beforeReadEvent->aborted()) {
            throw new BeforeReadAbortedException();
        }

        if ($sectionConfig === null && count($readOptions->getSection()) > 0) {
            /** @var FullyQualifiedClassName $section */
            $section = $readOptions->getSection()[0];
            $sectionConfig = $this->sectionManager->readByHandle(
                $section->toHandle()
            )->getConfig();
        }

        // Make sure we are passing the fully qualified class name as the section
        if (count($readOptions->getSection()) > 0) {
            $optionsArray = $readOptions->toArray();
            $optionsArray[ReadOptions::SECTION] = (string) $sectionConfig->getFullyQualifiedClassName();
            $readOptions = ReadOptions::fromArray($optionsArray);
        }

        /** @var ReadSectionInterface $reader */
        foreach ($this->readers as $reader) {
            foreach ($reader->readMaybe($readOptions, $sectionConfig) as $entry) {
                $sectionData->append($entry);
            }
        }

        $sectionDataRead = new SectionEntryDataRead($sectionData, $readOptions, $sectionConfig);
        $this->dispatcher->dispatch($sectionDataRead);
        if ($sectionDataRead->aborted()) {
            throw new ReadAbortedException();
        }

        return $sectionData;
    }

    /**
     * @inheritDoc
     */
    public function read(
        OptionsInterface $readOptions,
        SectionConfig $sectionConfig = null
    ): \ArrayIterator {
        $sectionData = new \ArrayIterator();

        $beforeReadEvent = new SectionEntryBeforeRead(
            $sectionData,
            $readOptions,
            $sectionConfig
        );
        $this->dispatcher->dispatch($beforeReadEvent);
        if ($beforeReadEvent->aborted()) {
            throw new BeforeReadAbortedException();
        }

        if ($sectionConfig === null && count($readOptions->getSection()) > 0) {
            /** @var FullyQualifiedClassName $section */
            $section = $readOptions->getSection()[0];
            $sectionConfig = $this->sectionManager->readByHandle(
                $section->toHandle()
            )->getConfig();
        }

        // Make sure we are passing the fully qualified class name as the section
        if (count($readOptions->getSection()) > 0) {
            $optionsArray = $readOptions->toArray();
            $optionsArray[ReadOptions::SECTION] = (string)$sectionConfig->getFullyQualifiedClassName();
            $readOptions = ReadOptions::fromArray($optionsArray);
        }

        /** @var ReadSectionInterface $reader */
        foreach ($this->readers as $reader) {
            foreach ($reader->read($readOptions, $sectionConfig) as $entry) {
                $sectionData->append($entry);
            }
        }

        $sectionDataRead = new SectionEntryDataRead($sectionData, $readOptions, $sectionConfig);
        $this->dispatcher->dispatch($sectionDataRead);
        if ($sectionDataRead->aborted()) {
            throw new ReadAbortedException();
        }

        return $sectionData;
    }

    public function flush(): void
    {
        foreach ($this->readers as $reader) {
            $reader->flush();
        }
    }
}
