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

use Tardigrades\SectionField\Event\BeforeReadAbortedException;
use Tardigrades\SectionField\Event\ReadAbortedException;
use Tardigrades\SectionField\ValueObject\SectionConfig;

interface ReadSectionInterface
{
    /**
     * Read from one or more data-sources
     *
     * @param ReadOptionsInterface $readOptions
     * @param SectionConfig|null $sectionConfig
     * @return \ArrayIterator
     * @throws BeforeReadAbortedException
     * @throws EntryNotFoundException
     * @throws ReadAbortedException
     */
    public function read(ReadOptionsInterface $readOptions, SectionConfig $sectionConfig = null): \ArrayIterator;

    public function flush(): void;
}
