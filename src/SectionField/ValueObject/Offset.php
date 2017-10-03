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

final class Offset
{
    /** @var int */
    private $offset;

    private function __construct(int $offset)
    {
        Assertion::integer($offset, 'Offset is supposed to be an integer');

        $this->offset = $offset;
    }

    public function toInt(): int
    {
        return $this->offset;
    }

    public static function fromInt(int $offset): self
    {
        return new self($offset);
    }
}
