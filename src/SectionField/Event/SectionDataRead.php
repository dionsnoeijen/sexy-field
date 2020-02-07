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

namespace Tardigrades\SectionField\Event;

use Symfony\Component\EventDispatcher\Event;
use Tardigrades\SectionField\Service\ReadOptionsInterface;
use Tardigrades\SectionField\ValueObject\SectionConfig;

/**
 * Class SectionDataRead
 *
 * After reading data from the readers, this event gives the opportunity to:
 * - prefill the data array iterator
 * - use / manipulate read options
 * - use / manipulate section config
 *
 * @package Tardigrades\SectionField\Event
 */
class SectionDataRead extends Event
{
    /** @var \ArrayIterator */
    private $data;

    /** @var ReadOptionsInterface */
    private $readOptions;

    /** @var ?SectionConfig */
    private $sectionConfig;

    /**
     * SectionDataRead constructor.
     * @param \ArrayIterator $data
     * @param ReadOptionsInterface $readOptions
     * @param SectionConfig $sectionConfig
     */
    public function __construct(
        \ArrayIterator $data,
        ReadOptionsInterface $readOptions,
        SectionConfig $sectionConfig = null
    ) {
        $this->data = $data;
        $this->readOptions = $readOptions;
        $this->sectionConfig = $sectionConfig;
    }

    /**
     * The array containing data from the readers
     *
     * @return \ArrayIterator
     */
    public function getData(): \ArrayIterator
    {
        return $this->data;
    }

    /**
     * Get read options for manipulation or other actions
     *
     * @return ReadOptionsInterface
     */
    public function getReadOptions(): ReadOptionsInterface
    {
        return $this->readOptions;
    }

    /**
     * Get section config for manipulation or other actions
     *
     * @return null|SectionConfig
     */
    public function getSectionConfig(): ?SectionConfig
    {
        return $this->sectionConfig;
    }
}
