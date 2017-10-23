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

final class Limit
{
    /** @var int */
    private $limit;

    private function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    public function toInt(): int
    {
        return $this->limit;
    }

    public static function fromInt(int $limit): self
    {
        return new self($limit);
    }
}
