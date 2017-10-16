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

class SectionEntryCreated extends Event
{
    const NAME = 'section.entry.created';

    protected $entry;

    /** @var bool */
    protected $success;

    public function __construct($entry, bool $success)
    {
        $this->entry = $entry;
        $this->success = $success;
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }
}
