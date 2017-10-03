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

namespace Tardigrades\SectionField\Generator;

use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Generator\Writer\Writable;

interface GeneratorInterface
{
    public function generateBySection(SectionInterface $section): Writable;
    public function getBuildMessages(): array;
}
