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

use Tardigrades\Entity\FieldInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;

interface FieldManagerInterface
{
    public function create(FieldInterface $entity): FieldInterface;
    public function read(Id $id): FieldInterface;
    /**
     * @return array
     * @throws FieldNotFoundException
     */
    public function readAll(): array;
    public function update(): void;
    public function delete(FieldInterface $entity): void;
    public function createByConfig(FieldConfig $fieldConfig): FieldInterface;
    public function updateByConfig(FieldConfig $fieldConfig, FieldInterface $field): FieldInterface;
    /**
     * @return array
     * @throws FieldNotFoundException
     */
    public function readByHandle(Handle $handle): FieldInterface;
    /**
     * @return array
     * @throws FieldNotFoundException
     */
    public function readByHandles(array $fields): array;
}
