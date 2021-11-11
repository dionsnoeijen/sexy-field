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

use Symfony\Contracts\EventDispatcher\Event;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\OptionsInterface;

class SectionEntryDeleted extends Event
{
    protected CommonSectionInterface $entry;
    protected bool $success;
    protected ?OptionsInterface $deleteOptions;

    public function __construct(
        CommonSectionInterface $entry,
        bool $success,
        ?OptionsInterface $deleteOptions = null
    ) {
        $this->entry = $entry;
        $this->success = $success;
        $this->deleteOptions = $deleteOptions;
    }

    public function getEntry(): CommonSectionInterface
    {
        return $this->entry;
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }

    public function getDeleteOptions(): ?OptionsInterface
    {
        return $this->deleteOptions;
    }
}
