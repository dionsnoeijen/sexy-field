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

/**
 * Class SectionEntryDeleted
 *
 * Right after the section entry entity is deleted from it's data sources, this event is dispatched.
 *
 * @package Tardigrades\SectionField\Event
 */
class SectionEntryDeleted extends Event
{
    const NAME = 'section.entry.deleted';

    /** @var  CommonSectionInterface */
    protected $entry;

    /** @var bool */
    protected $success;

    public function __construct(CommonSectionInterface $entry, bool $success)
    {
        $this->entry = $entry;
        $this->success = $success;
    }

    /**
     * This entry was deleted.
     *
     * @return CommonSectionInterface
     */
    public function getEntry(): CommonSectionInterface
    {
        return $this->entry;
    }

    /**
     * Has it been successful?
     *
     * @return bool
     */
    public function getSuccess(): bool
    {
        return $this->success;
    }
}
