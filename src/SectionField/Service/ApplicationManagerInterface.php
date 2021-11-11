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

use Tardigrades\Entity\ApplicationInterface;
use Tardigrades\SectionField\ValueObject\ApplicationConfig;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;

interface ApplicationManagerInterface
{
    public function create(ApplicationInterface $entity): ApplicationInterface;
    public function read(Id $id): ApplicationInterface;
    public function readByHandle(Handle $handle): ApplicationInterface;
    /**
     * @return array
     * @throws ApplicationNotFoundException
     */
    public function readAll(): array;
    public function update(): void;
    public function delete(ApplicationInterface $entity): void;
    public function createByConfig(ApplicationConfig $applicationConfig): ApplicationInterface;
    public function updateByConfig(
        ApplicationConfig $applicationConfig,
        ApplicationInterface $application
    ): ApplicationInterface;
}
