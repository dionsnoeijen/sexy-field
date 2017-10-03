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

use Tardigrades\SectionField\ValueObject\SectionConfig;

interface ReadSectionInterface
{
    public function read(ReadOptionsInterface $options, SectionConfig $sectionConfig = null): \ArrayIterator;
}
