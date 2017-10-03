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

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\Updated;
use Tardigrades\SectionField\ValueObject\Version;

class Section extends SectionBase implements SectionInterface, SectionEntityInterface
{
    /** @var ArrayCollection */
    protected $history;

    public function __construct(
        Collection $fields = null,
        Collection $applications = null,
        Collection $history = null
    ) {
        parent::__construct(
            is_null($fields) ? new ArrayCollection() : $fields,
            is_null($applications) ? new ArrayCollection() : $applications
        );

        $this->history = is_null($history) ? new ArrayCollection() : $history;
    }

    public function getHistory(): Collection
    {
        return $this->history;
    }

    public function addHistory(SectionHistoryInterface $section): SectionEntityInterface
    {
        if ($this->history->contains($section)) {
            return $this;
        }
        $this->history->add($section);
        $section->setSection($this);

        return $this;
    }

    public function removeHistory(SectionInterface $section): SectionEntityInterface
    {
        if (!$this->history->contains($section)) {
            return $this;
        }
        $this->history->remove($section);

        return $this;
    }

    public function onPrePersist(): void
    {
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
    }

    public function onPreUpdate(): void
    {
        $this->updated = new \DateTime("now");
    }
}
