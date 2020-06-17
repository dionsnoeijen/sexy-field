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
use Tardigrades\SectionField\Service\OptionsInterface;

class SectionEntryBeforeDelete extends Event
{
    /** @var CommonSectionInterface */
    private $entry;

    /** @var bool */
    private $aborted = false;

    /** @var OptionsInterface */
    private $options;

    public function __construct(
        CommonSectionInterface $entry,
        ?OptionsInterface $options = null
    ) {
        $this->entry = $entry;
        $this->options = $options;
    }

    public function getEntry(): CommonSectionInterface
    {
        return $this->entry;
    }

    public function abort(): void
    {
        $this->aborted = true;
    }

    public function aborted(): bool
    {
        return $this->aborted;
    }

    public function getDeleteOptions(): ?OptionsInterface
    {
        return $this->options;
    }
}
