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

use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\SectionConfig;

interface SectionManagerInterface
{
    public function create(SectionInterface $entity): SectionInterface;
    public function read(Id $id): SectionInterface;
    public function readByIds(array $ids): array;
    /**
     * @return array
     * @throws SectionNotFoundException
     */
    public function readAll(): array;
    public function update(SectionInterface $entity): void;
    public function delete(SectionInterface $entity): void;
    public function createByConfig(SectionConfig $sectionConfig): SectionInterface;
    public function updateByConfig(
        SectionConfig $sectionConfig,
        SectionInterface $section,
        bool $history = true
    ): SectionInterface;
    public function restoreFromHistory(SectionInterface $sectionFromHistory): SectionInterface;
    public function getRelationshipsOfAll(): array;
    /**
     * @param Handle $handle
     * @return SectionInterface
     * @throws SectionNotFoundException
     */
    public function readByHandle(Handle $handle): SectionInterface;
    public function readByHandles(array $handles): array;
}
