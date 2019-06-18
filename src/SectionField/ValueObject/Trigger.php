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

    /** @var FullyQualifiedClassName */
    private $service;

    /** @var CommonSectionInterface */
    private $entry;

    private function __construct(
        string $name,
        FullyQualifiedClassName $service,
        CommonSectionInterface $entry
    ) {
        $this->name = $name;
        $this->service = $service;
        $this->entry = $entry;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getService(): FullyQualifiedClassName
    {
        return $this->service;
    }

    public function getEntry(): CommonSectionInterface
    {
        return $this->entry;
    }

    public function __toString()
    {
        return $this->name;
    }

    public static function fromNameAndService(
        string $name,
        FullyQualifiedClassName $service,
        CommonSectionInterface $entry
    ): self {
        return new self($name, $service, $entry);
    }
}
