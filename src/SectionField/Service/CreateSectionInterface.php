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

use Tardigrades\SectionField\Event\BeforeCreateAbortedException;
use Tardigrades\SectionField\Event\BeforeUpdateAbortedException;
use Tardigrades\SectionField\Generator\CommonSectionInterface;

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
     * @param CommonSectionInterface $sectionEntryEntity
     * @param OptionsInterface|null $createOptions
     */
    public function save(
        CommonSectionInterface $sectionEntryEntity,
        ?OptionsInterface $createOptions = null
    );

    /**
     * This method goes through all writers to persist a record, like save, but doesn't flush them.
     *
     * It's useful when many records have to be persisted. flush has to be called manually afterwards.
     *
     * No events are dispatched.
     *
     * @param CommonSectionInterface $sectionEntryEntity
     * @throws BeforeCreateAbortedException
     * @throws BeforeUpdateAbortedException
     */
    public function persist(CommonSectionInterface $sectionEntryEntity);

    /**
     * This method flushes all writers to store records that have been persisted.
     *
     * No events are dispatched.
     */
    public function flush();
}
