<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;
use Tardigrades\Helper\ArrayConverter;

final class FieldMetadata
{
    /**
     * @var array
     */
    private $metadata;

    private function __construct(array $metadata)
    {
        Assertion::isArray($metadata, 'Metadata not defined');

        $this->metadata = $metadata;
    }

    public function toArray(): array
    {
        return $this->metadata;
    }

    public function __toString(): string
    {
        return ArrayConverter::recursive($this->metadata);
    }

    public static function fromArray(array $metadata): self
    {
        return new self($metadata);
    }
}
