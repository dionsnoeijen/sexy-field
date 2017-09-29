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

use Assert\Assertion;

final class FieldValue
{
    /** @var Handle */
    private $handle;

    /** @var string */
    private $value;

    private function __construct(Handle $handle, string $value)
    {
        Assertion::string($value, 'The value is supposed to be a string');

        $this->handle = $handle;
        $this->value = $value;
    }

    public function toArray(): array
    {
        return [(string) $this->handle, $this->value];
    }

    public static function fromHandleAndValue(Handle $handle, string $value): self
    {
        return new self($handle, $value);
    }
}
