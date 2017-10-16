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
 * Class SectionBeforeRead
 *
 * Before the readers are called this event gives the opportunity to:
 * - prefill the data array iterator
 * - use / manipulate read options
 * - use / manipulate section config
 *
 * @package Tardigrades\SectionField\Event
 */
class SectionBeforeRead extends Event
{
    const NAME = 'section.before.read';

    /** @var \ArrayIterator */
    private $data;

    /** @var ReadOptionsInterface */
    private $readOptions;

    /** @var SectionConfig */
    private $sectionConfig;

    public function __construct(\ArrayIterator $data, ReadOptionsInterface $readOptions, SectionConfig $sectionConfig)
    {
        $this->data = $data;
        $this->readOptions = $readOptions;
        $this->sectionConfig = $sectionConfig;
    }

    /**
     * @return \ArrayIterator
     */
    public function getData()
    {
        return $this->data;
    }

    public function getReadOptions(): ReadOptionsInterface
    {
        return $this->readOptions;
    }

    public function getSectionConfig(): SectionConfig
    {
        return $this->sectionConfig;
    }
}
