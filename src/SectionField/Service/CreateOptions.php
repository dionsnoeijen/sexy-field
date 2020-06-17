<?php
declare(strict_types=1);

namespace Tardigrades\SectionField\Service;

use Assert\Assertion;
use Assert\AssertionFailedException;

class CreateOptions implements OptionsInterface
{
    const METADATA = 'metadata';

    /** @var array */
    protected $options;

    public function __construct(
        array $options
    ) {
        $this->options = $options;
    }

    public function getMetadata(): ?array
    {
        try {
            Assertion::keyIsset($this->options, self::METADATA, 'No metadata');
            Assertion::notEmpty(
                $this->options[self::METADATA],
                'Metadata is empty.'
            );
            Assertion::isArray($this->options[self::METADATA]);
        } catch (AssertionFailedException $exception) {
            return null;
        }

        return $this->options[self::METADATA];
    }

    public static function fromArray(array $options): OptionsInterface
    {
        return new static($options);
    }

    public function toArray(): array
    {
        return $this->options;
    }
}
