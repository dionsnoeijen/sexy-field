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

final class UpdatedField
{
    /** @var string */
    private $updatedField = '';

    private function __construct(string $value)
    {
        Assertion::nullOrNotEmpty($value, 'Value is not specified');

        $this->updatedField = $value;
    }

    public function __toString(): string
    {
        return $this->updatedField;
    }

    public static function fromString(string $updatedField): self
    {
        return new static($updatedField);
    }
}
