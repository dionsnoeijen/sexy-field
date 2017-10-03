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

use Doctrine\Common\Collections\Collection;

interface SectionEntityInterface
{
    public function getHistory(): Collection;
    public function addHistory(SectionHistoryInterface $section): SectionEntityInterface;
    public function removeHistory(SectionInterface $section): SectionEntityInterface;
    public function onPrePersist(): void;
    public function onPreUpdate(): void;
}
