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

final class JitRelationship
{
    /** @var FullyQualifiedClassName */
    private $fullyQualifiedClassName;

    /** @var Id */
    private $id;

    private function __construct(
        FullyQualifiedClassName $fullyQualifiedClassName,
        Id $id
    ) {
        $this->fullyQualifiedClassName = $fullyQualifiedClassName;
        $this->id = $id;
    }

    public function getFullyQualifiedClassName()
    {
        return $this->fullyQualifiedClassName;
    }

    public function getId()
    {
        return $this->id;
    }

    public static function fromFullyQualifiedClassNameAndId(
        FullyQualifiedClassName $fullyQualifiedClassName,
        Id $id
    ): self {
        return new self($fullyQualifiedClassName, $id);
    }
}
