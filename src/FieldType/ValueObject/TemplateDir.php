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

namespace Tardigrades\FieldType\ValueObject;

use Assert\Assertion;

final class TemplateDir
{
    /** @var string */
    private $templateDir;

    private function __construct(string $templateDir)
    {
        Assertion::string($templateDir, 'The template dir has to be a string');

        $this->templateDir = $templateDir;
    }

    public function __toString(): string
    {
        return $this->templateDir;
    }

    public static function fromString(string $templateDir): self
    {
        return new self($templateDir);
    }
}
