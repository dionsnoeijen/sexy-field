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

namespace Tardigrades\SectionField\Service;

use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Type;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\Entity\FieldTypeInterface;

interface FieldTypeManagerInterface
{
    public function create(FieldTypeInterface $entity): FieldTypeInterface;
    public function read(Id $id): FieldTypeInterface;
    /**
     * @return array
     * @throws FieldTypeNotFoundException
     */
    public function readAll(): array;
    public function update(): void;
    public function delete(FieldTypeInterface $entity): void;
    public function createWithFullyQualifiedClassName(
        FullyQualifiedClassName $fullyQualifiedClassName
    ): FieldTypeInterface;

    /**
     * @param Type $type
     * @return FieldTypeInterface
     * @throws FieldTypeNotFoundException
     */
    public function readByType(Type $type): FieldTypeInterface;

    /**
     * @param FullyQualifiedClassName $fullyQualifiedClassName
     * @return FieldTypeInterface
     * @throws FieldTypeNotFoundException
     */
    public function readByFullyQualifiedClassName(
        FullyQualifiedClassName $fullyQualifiedClassName
    ): FieldTypeInterface;
}
