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

namespace Tardigrades\SectionField\Generator;

use Symfony\Component\Validator\Mapping\ClassMetadata;

interface CommonSectionInterface
{
    const FIELDS = [];
    public function getId(): ?int;
    public static function loadValidatorMetadata(ClassMetadata $metadata): void;
    public function onPrePersist(): void;
    public function onPreUpdate(): void;
}
