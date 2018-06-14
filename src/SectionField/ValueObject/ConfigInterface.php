<?php
declare(strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

interface ConfigInterface
{
    public static function fromArray(array $config);
    public function toArray(): array;
    public function __toString(): string;
}
