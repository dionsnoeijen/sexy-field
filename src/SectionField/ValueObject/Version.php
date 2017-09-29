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

final class Version
{
    /** @var int */
    private $version;

    private function __construct(int $version)
    {
        $this->version = $version;
    }

    public function __toString(): string
    {
        return (string) $this->version;
    }

    public function toInt(): int
    {
        return $this->version;
    }

    public static function fromInt(int $version): self
    {
        return new self($version);
    }
}
