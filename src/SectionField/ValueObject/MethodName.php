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
use Doctrine\Common\Util\Inflector;

final class MethodName
{
    /**
     * @var string
     */
    private $methodName;

    private function __construct(string $methodName)
    {
        Assertion::string($methodName, 'The MethodName has to be a string');

        $this->methodName = $methodName;
    }

    public function __toString(): string
    {
        return $this->methodName;
    }

    public static function fromString(string $methodName): self
    {
        $methodName = Inflector::camelize($methodName);

        return new self(ucfirst($methodName));
    }
}
