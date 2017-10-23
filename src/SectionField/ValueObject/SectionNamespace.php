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

final class SectionNamespace
{
    /** @var string */
    private $sectionNamespace;

    private function __construct(string $sectionNamespace)
    {
        $this->sectionNamespace = $sectionNamespace;
    }

    public function __toString(): string
    {
        return $this->sectionNamespace;
    }

    public static function fromString(string $sectionNamespace): self
    {
        return new self($sectionNamespace);
    }
}
