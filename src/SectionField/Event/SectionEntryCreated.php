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
use Tardigrades\SectionField\Generator\CommonSectionInterface;

class SectionEntryCreated extends Event
{
    /** @var CommonSectionInterface */
    protected $entry;

    /** @var bool */
    protected $update;

    public function __construct(CommonSectionInterface $entry, bool $update)
    {
        $this->entry = $entry;
        $this->update = $update;
    }

    public function getEntry(): CommonSectionInterface
    {
        return $this->entry;
    }

    public function getUpdate(): bool
    {
        return $this->update;
    }
}
