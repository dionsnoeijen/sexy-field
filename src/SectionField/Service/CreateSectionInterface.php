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
 * Class CreateSection
 *
 * The entry point for saving a section entry entity.
 *
 * @package Tardigrades\SectionField\Service
 */
interface CreateSectionInterface
{
    /**
     * This save method goes through all available writers to store a section record
     *
     * Before doing that, it will dispatch a SectionEntryBeforeCreate event, so one might
     * change the entity before it's stored.
     *
     * Afterwards, another event is dispatched, letting all listeners know that writing is done.
     *
     * There is a success true, boolean. This will be enhanced later on.
     *
     * @param $sectionEntryEntity
     * @param array|null $jitRelationships
     */
    public function save($data, array $jitRelationships = null);
}
