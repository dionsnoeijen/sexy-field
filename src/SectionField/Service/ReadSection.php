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
use Tardigrades\SectionField\Event\SectionBeforeRead;
use Tardigrades\SectionField\Event\SectionDataRead;
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

    /**
     * Read from one or more data-sources
     *
     * @param ReadOptionsInterface $options
     * @param SectionConfig|null $sectionConfig
     * @return \ArrayIterator
     */
    public function read(
        ReadOptionsInterface $options,
        SectionConfig $sectionConfig = null
    ): \ArrayIterator {
        $sectionData = new \ArrayIterator();

        $this->dispatcher->dispatch(
            SectionBeforeRead::NAME,
            new SectionBeforeRead($sectionData, $options, $sectionConfig)
        );

        if ($sectionConfig === null) {
            $sectionConfig = $this->sectionManager->readByHandle(
                $options->getSection()[0]->toHandle()
            )->getConfig();
        }

        // Make sure we are passing the fully qualified class name as the section
        $optionsArray = $options->toArray();
        $optionsArray[ReadOptions::SECTION] = (string) $sectionConfig->getFullyQualifiedClassName();
        // For now, we call DoctrineRead options, this will of course be fixed in a later release.
        $options = ReadOptions::fromArray($optionsArray);

        /** @var ReadSectionInterface $reader */
        foreach ($this->readers as $reader) {
            foreach ($reader->read($options, $sectionConfig) as $entry) {
                $sectionData->append($entry);
            }
        }

        $this->dispatcher->dispatch(
            SectionDataRead::NAME,
            new SectionDataRead($sectionData, $options, $sectionConfig)
        );

        return $sectionData;
    }

    public function flush(): void
    {
        foreach ($this->readers as $reader) {
            $reader->flush();
        }
    }
}
