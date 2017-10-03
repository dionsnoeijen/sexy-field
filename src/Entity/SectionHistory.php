<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types=1);

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
use Tardigrades\SectionField\ValueObject\Versioned;

class SectionHistory extends SectionBase implements SectionInterface, SectionHistoryInterface
{
    /** @var SectionInterface|null */
    private $section;

    /** @var \DateTime */
    protected $versioned;

    public function getVersioned(): Versioned
    {
        return Versioned::fromDateTime($this->versioned);
    }

    public function setVersioned(\DateTime $versioned): SectionHistoryInterface
    {
        $this->versioned = $versioned;

        return $this;
    }

    public function setSection(SectionInterface $section): SectionHistoryInterface
    {
        $this->section = $section;

        return $this;
    }

    public function getSection(): ?SectionInterface
    {
        return $this->section;
    }

    public function removeSection(): SectionHistoryInterface
    {
        $this->section = null;

        return $this;
    }
}
