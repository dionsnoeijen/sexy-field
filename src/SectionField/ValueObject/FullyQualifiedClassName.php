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

final class FullyQualifiedClassName
{
    /** @var string */
    private $fullyQualifiedClassName;

    private function __construct(string $fullyQualifiedClassName)
    {
        $this->fullyQualifiedClassName = str_replace('.', '\\', $fullyQualifiedClassName);
        $this->fullyQualifiedClassName = str_replace('Proxies\\__CG__\\', '', $this->fullyQualifiedClassName);
    }

    public function getClassName()
    {
        $type = explode('\\', $this->fullyQualifiedClassName);
        $className = $type[count($type) - 1];

        return $className;
    }

    public function __toString(): string
    {
        return $this->fullyQualifiedClassName;
    }

    public function toHandle(): Handle
    {
        $handle = explode('\\', $this->fullyQualifiedClassName);
        $handle = end($handle);
        return Handle::fromString(lcfirst($handle));
    }

    public static function fromNamespaceAndClassName(SectionNamespace $namespace, ClassName $className)
    {
        return new self((string) $namespace . '\\Entity\\' . (string) $className);
    }

    public static function fromString(string $fullyQualifiedClassName): self
    {
        return new self($fullyQualifiedClassName);
    }
}
