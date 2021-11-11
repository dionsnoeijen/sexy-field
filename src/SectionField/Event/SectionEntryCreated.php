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

class SectionEntryCreated extends Event
{
    protected CommonSectionInterface $entry;
    protected bool $update;
    protected ?OptionsInterface $createOptions;

    public function __construct(
        CommonSectionInterface $entry,
        bool $update,
        ?OptionsInterface $createOptions = null
    ) {
        $this->entry = $entry;
        $this->update = $update;
        $this->createOptions = $createOptions;
    }

    public function getEntry(): CommonSectionInterface
    {
        return $this->entry;
    }

    public function getUpdate(): bool
    {
        return $this->update;
    }

    public function getCreateOptions(): ?OptionsInterface
    {
        return $this->createOptions;
    }
}
