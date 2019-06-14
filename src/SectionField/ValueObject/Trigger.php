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

use Tardigrades\SectionField\Generator\CommonSectionInterface;

final class Trigger
{
    /** @var string */
    private $name;

    /** @var array */
    private $arguments;

    /** @var CommonSectionInterface */
    private $entry;

    private function __construct(
        string $name,
        array $arguments,
        CommonSectionInterface $entry
    ) {
        $this->name = $name;
        $this->arguments = $arguments;
        $this->entry = $entry;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getEntry(): CommonSectionInterface
    {
        return $this->entry;
    }

    public function __toString()
    {
        return $this->name;
    }

    public static function fromNameAndArguments(
        string $name,
        array $arguments,
        CommonSectionInterface $entry
    ): self {
        return new self($name, $arguments, $entry);
    }
}
