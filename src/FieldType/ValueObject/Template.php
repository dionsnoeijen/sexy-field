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

namespace Tardigrades\FieldType\ValueObject;

final class Template
{
    /** @var string */
    private $template;

    private function __construct(string $template)
    {
        $this->template = $template;
    }

    public function __toString(): string
    {
        return $this->template;
    }

    public static function create(string $template): self
    {
        return new self($template);
    }
}
