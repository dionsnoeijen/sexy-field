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

namespace Tardigrades\SectionField\ValueObject;

final class Updated
{
    /** @var \DateTime */
    private $updated;

    private function __construct(\DateTime $updated)
    {
        $this->updated = $updated;
    }

    public function __toString(): string
    {
        return $this->updated->format(\DateTime::ATOM);
    }

    public function getDateTime(): \DateTime
    {
        return $this->updated;
    }

    public static function fromDateTime(\DateTime $updated): self
    {
        return new self($updated);
    }
}
