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

/**
 * Class DeleteSection
 *
 * This is the entry point for deleting a section entry entity.
 *
 * @package Tardigrades\SectionField\Service
 */
interface DeleteSectionInterface
{
    /**
     * This delete method loops through all deleters, so this section entry entity is deleted throughout all sources.
     *
     * @param $sectionEntryEntity
     * @return bool
     */
    public function delete($sectionEntryEntity): bool;
}
