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

final class ApplicationConfig implements ConfigWithHandleInterface
{
    /** @var array */
    private $applicationConfig;

    private function __construct(array $applicationConfig)
    {
        Assertion::keyIsset($applicationConfig, 'application', 'Config is not a application config');
        Assertion::keyIsset($applicationConfig['application'], 'name', 'The name for the application is required.');
        Assertion::keyIsset($applicationConfig['application'], 'handle', 'The handle for the application is required.');
        Assertion::keyIsset($applicationConfig['application'], 'languages', 'At least define one language in an array');
        Assertion::isArray($applicationConfig['application']['languages'], 'Languages should contain an array');

        $this->applicationConfig = $applicationConfig;
    }

    public function getHandle(): Handle
    {
        return Handle::fromString($this->applicationConfig['application']['handle']);
    }

    public function toArray(): array
    {
        return $this->applicationConfig;
    }

    public function __toString(): string
    {
        return ArrayConverter::recursive($this->applicationConfig);
    }

    public static function fromArray(array $applicationConfig): self
    {
        return new self($applicationConfig);
    }
}
