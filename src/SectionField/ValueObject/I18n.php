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

final class I18n
{
    /** @var string */
    private $i18n;

    private function __construct(string $i18n)
    {
        Assertion::string($i18n, 'The i18n must be a string');

        $this->i18n = $i18n;
    }

    public function __toString()
    {
        return $this->i18n;
    }

    public static function fromString(string $i18n): self
    {
        return new self($i18n);
    }
}
