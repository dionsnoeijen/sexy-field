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

final class Id
{
    /** @var int */
    private $id;

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function toInt(): int
    {
        return (int) $this->id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public static function fromInt(int $id): self
    {
        return new self($id);
    }
}
