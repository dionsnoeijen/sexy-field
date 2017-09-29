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

namespace Tardigrades\SectionField\ValueObject;

final class Created
{
    /** @var \DateTime */
    private $created;

    private function __construct(\DateTime $created)
    {
        $this->created = $created;
    }

    public function __toString(): string
    {
        return $this->created->format(\DateTime::ATOM);
    }

    public function getDateTime(): \DateTime
    {
        return $this->created;
    }

    public static function fromDateTime(\DateTime $created): self
    {
        return new self($created);
    }
}
