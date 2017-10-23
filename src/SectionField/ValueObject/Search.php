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

final class Search
{
    /** @var string */
    private $search;

    private function __construct(string $search)
    {
        $this->search = $search;
    }

    public function __toString(): string
    {
        return $this->search;
    }

    public static function fromString(string $search): self
    {
        return new self($search);
    }
}
