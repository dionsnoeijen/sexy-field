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

use Assert\Assertion;

final class Sort
{
    const ASC = 'asc';
    const DESC = 'desc';

    /** @var string */
    private $sort;

    private function __construct(string $sort)
    {
        Assertion::choice($sort, [self::ASC, self::DESC], 'Sort option incorrect');

        $this->sort = $sort;
    }

    public function __toString(): string
    {
        return $this->sort;
    }

    public static function fromString(string $sort): self
    {
        return new self($sort);
    }
}
