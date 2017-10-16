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
 * Class SectionEntryCreated
 *
 * Dispatched right after all writers have persisted a section entry entity.
 *
 * @package Tardigrades\SectionField\Event
 */
class SectionEntryCreated extends Event
{
    const NAME = 'section.entry.created';

    /** A Section Entry Entity */
    protected $entry;

    /** @var bool */
    protected $update;

    public function __construct($entry, bool $update)
    {
        $this->entry = $entry;
        $this->update = $update;
    }

    /**
     * The Section Entry Entity that was just persisted
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Was it a create or update action?
     *
     * @return bool
     */
    public function getUpdate(): bool
    {
        return $this->update;
    }
}
