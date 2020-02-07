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

class SectionEntryBeforeRead extends Event
{
    /** @var \ArrayIterator */
    private $data;

    /** @var ReadOptionsInterface */
    private $readOptions;

    /** @var SectionConfig */
    private $sectionConfig;

    /** @var bool */
    private $aborted = false;

    public function __construct(
        \ArrayIterator $data,
        ReadOptionsInterface $readOptions,
        SectionConfig $sectionConfig = null
    ) {
        $this->data = $data;
        $this->readOptions = $readOptions;
        $this->sectionConfig = $sectionConfig;
    }

    public function getData(): \ArrayIterator
    {
        return $this->data;
    }

    public function getReadOptions(): ReadOptionsInterface
    {
        return $this->readOptions;
    }

    public function getSectionConfig(): ?SectionConfig
    {
        return $this->sectionConfig;
    }

    public function abort(): void
    {
        $this->aborted = true;
    }

    public function aborted(): bool
    {
        return $this->aborted;
    }
}
