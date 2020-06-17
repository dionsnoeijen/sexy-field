<?php
declare(strict_types=1);

namespace Tardigrades\SectionField\Service;

interface OptionsInterface
{
    public static function fromArray(array $options): self;
    public function toArray(): array;
}
