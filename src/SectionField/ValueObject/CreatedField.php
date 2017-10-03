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

final class CreatedField
{
    /** @var string */
    private $createdField = '';

    private function __construct(string $value)
    {
        Assertion::nullOrNotEmpty($value, 'Value is not specified');

        $this->createdField = $value;
    }

    public function __toString(): string
    {
        return $this->createdField;
    }

    public static function fromString(string $createdField): self
    {
        return new static($createdField);
    }
}
