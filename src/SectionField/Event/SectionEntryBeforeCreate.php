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

/**
 * Class SectionEntryBeforeCreate
 *
 * This event is dispatched right before the persistance of a section entry. It contains the entity so you can use or manipulate it.
 *
 * @package Tardigrades\SectionField\Event
 */
class SectionEntryBeforeCreate extends Event
{
    const NAME = 'section.entry.before.create';

    /** A Section Entry Entity */
    protected $entry;

    public function __construct($entry)
    {
        $this->entry = $entry;
    }

    /**
     * The entity that is about to be persisted.
     */
    public function getEntry()
    {
        return $this->entry;
    }
}
