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
use Tardigrades\Helper\ArrayConverter;

final class FieldTypeGeneratorConfig
{
    /**
     * @var array
     */
    private $config;

    private function __construct(array $config)
    {
        Assertion::isArray($config, 'Generator config not defined');

        $this->config = $config;
    }

    public function toArray(): array
    {
        return $this->config;
    }

    public function __toString(): string
    {
        return ArrayConverter::recursive($this->config);
    }

    public static function fromArray(array $config): self
    {
        return new self($config);
    }
}
