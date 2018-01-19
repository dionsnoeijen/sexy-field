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

final class FieldConfig
{
    /** @var array */
    private $fieldConfig;

    private function __construct(array $fieldConfig)
    {
        Assertion::keyIsset($fieldConfig, 'field', 'Config is not a field config');
        Assertion::keyIsset($fieldConfig['field'], 'name', 'No name in config');
        Assertion::keyIsset($fieldConfig['field'], 'handle', 'Field needs a handle');
        Assertion::notEmpty($fieldConfig['field'], 'name', 'Field has no value');
        Assertion::notEmpty($fieldConfig['field'], 'handle', 'Field needs a handle');

        $this->fieldConfig = $fieldConfig;
    }

    public function toArray(): array
    {
        return $this->fieldConfig;
    }

    public function getName(): Name
    {
        return Name::fromString($this->fieldConfig['field']['name']);
    }

    public function getHandle(): Handle
    {
        return Handle::fromString($this->fieldConfig['field']['handle']);
    }

    public function getMethodName(): MethodName
    {
        return MethodName::fromString($this->fieldConfig['field']['handle']);
    }

    public function getRelationshipKind(): string
    {
        Assertion::keyIsset($this->fieldConfig['field'], 'kind', 'No relationship kind defined');
        Assertion::notEmpty($this->fieldConfig['field']['kind'], 'Relationship kind is empty');
        Assertion::string($this->fieldConfig['field']['kind'], 'Relationship kind must be defined as string');

        return $this->fieldConfig['field']['kind'];
    }

    public function getRelationshipTo(): string
    {
        Assertion::keyIsset($this->fieldConfig['field'], 'to', 'No relationship to defined');
        Assertion::notEmpty($this->fieldConfig['field']['to'], 'Relationship to is empty');
        Assertion::string($this->fieldConfig['field']['to'], 'Relationship to must be defined as string');

        return $this->fieldConfig['field']['to'];
    }

    public function getPropertyName(): PropertyName
    {
        return PropertyName::fromString($this->fieldConfig['field']['handle']);
    }

    public function getEntityEvents(): array
    {
        Assertion::keyExists($this->fieldConfig['field'], 'generator', 'No generator entity events found');
        Assertion::keyExists($this->fieldConfig['field']['generator'], 'entity', 'No generator entity events found');
        Assertion::keyExists($this->fieldConfig['field']['generator']['entity'], 'event', 'No generator entity events found');

        Assertion::isArray(
            $this->fieldConfig['field']['generator']['entity']['event'],
            'Entity events should be an array of events you want a generator to run for.'
        );

        return $this->fieldConfig['field']['generator']['entity']['event'];
    }

    public function getGeneratorConfig(): GeneratorConfig
    {
        return GeneratorConfig::fromArray($this->fieldConfig['field']);
    }

    public function getMetadata(): FieldMetadata
    {
        return FieldMetadata::fromArray($this->fieldConfig['metadata']);
    }

    public function __toString(): string
    {
        return ArrayConverter::recursive($this->fieldConfig['field']);
    }

    public static function fromArray(array $fieldConfig): self
    {
        return new self($fieldConfig);
    }
}
