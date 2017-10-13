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

use Doctrine\ORM\EntityManagerInterface;
use Tardigrades\Entity\Field;
use Tardigrades\Entity\FieldInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Type;

class DoctrineFieldManager implements FieldManagerInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var FieldTypeManagerInterface */
    private $fieldTypeManager;

    /** @var LanguageManagerInterface */
    private $languageManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        FieldTypeManagerInterface $fieldTypeManager,
        LanguageManagerInterface $languageManager
    ) {
        $this->entityManager = $entityManager;
        $this->fieldTypeManager = $fieldTypeManager;
        $this->languageManager = $languageManager;
    }

    public function create(FieldInterface$entity): FieldInterface
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function read(Id $id): FieldInterface
    {
        $fieldRepository = $this->entityManager->getRepository(Field::class);

        /** @var $field Field */
        $field = $fieldRepository->find($id->toInt());

        if (empty($field)) {
            throw new FieldNotFoundException();
        }

        return $field;
    }

    public function readAll(): array
    {
        $fieldRepository = $this->entityManager->getRepository(Field::class);
        $fields = $fieldRepository->findAll();

        if (empty($fields)) {
            throw new FieldNotFoundException();
        }

        return $fields;
    }

    public function update(): void
    {
        $this->entityManager->flush();
    }

    public function delete(FieldInterface $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function createByConfig(FieldConfig $fieldConfig): FieldInterface
    {
        $field = $this->setUpFieldByConfig($fieldConfig, new Field());

        $this->entityManager->persist($field);
        $this->entityManager->flush();

        return $field;
    }

    public function updateByConfig(FieldConfig $fieldConfig, FieldInterface $field): FieldInterface
    {
        $field = $this->setUpFieldByConfig($fieldConfig, $field);

        $this->entityManager->flush();

        return $field;
    }

    private function setUpFieldByConfig(FieldConfig $fieldConfig, FieldInterface $field): FieldInterface
    {
        $fieldConfig = $fieldConfig->toArray();
        $fieldType = $this->fieldTypeManager->readByType(Type::fromString($fieldConfig['field']['type']));

        $field->setName($fieldConfig['field']['name']);
        $field->setHandle($fieldConfig['field']['handle']);
        $field->setFieldType($fieldType);
        $field->setConfig($fieldConfig);

        return $field;
    }

    public function readByHandle(Handle $handle): FieldInterface
    {
        $fieldRepository = $this->entityManager->getRepository(Field::class);

        $field = $fieldRepository->findBy(['handle' => $handle]);

        if (empty($field)) {
            throw new FieldNotFoundException();
        }

        return $field[0];
    }

    public function readByHandles(array $handles): array
    {
        $fieldHandles = [];
        foreach ($handles as $handle) {
            $fieldHandles[] = '\'' . $handle . '\'';
        }
        $whereIn = implode(',', $fieldHandles);
        $query = $this->entityManager->createQuery(
            "SELECT field FROM Tardigrades\Entity\Field field WHERE field.handle IN ({$whereIn})"
        );
        $results = $query->getResult();
        if (empty($results)) {
            throw new FieldNotFoundException();
        }

        return $results;
    }
}
