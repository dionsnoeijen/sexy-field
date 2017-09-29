<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types=1);

namespace Tardigrades\Entity;

use Tardigrades\SectionField\ValueObject\Versioned;

interface SectionHistoryInterface
{
    public function setSection(SectionInterface $section): SectionHistoryInterface;
    public function getSection(): SectionInterface;
    public function removeSection(SectionInterface $section): SectionHistoryInterface;
    public function setVersioned(\DateTime $versioned): SectionHistoryInterface;
    public function getVersioned(): Versioned;
}
