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

final class LanguageConfig
{
    /** @var array */
    private $languageConfig;

    private function __construct(array $languageConfig)
    {
        Assertion::keyIsset($languageConfig, 'language', 'Config is not a language config');
        Assertion::isArray($languageConfig['language'], 'The languages should consist of an array');

        $this->languageConfig = $languageConfig;
    }

    public function toArray(): array
    {
        return $this->languageConfig;
    }

    public function __toString(): string
    {
        return ArrayConverter::recursive($this->languageConfig['language']);
    }

    public static function fromArray(array $languageConfig): self
    {
        return new self($languageConfig);
    }
}
