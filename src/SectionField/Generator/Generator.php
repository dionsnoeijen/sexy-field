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

use Assert\Assertion;
use Psr\Container\ContainerInterface;
use Tardigrades\Entity\Field as FieldEntity;
use Tardigrades\Entity\FieldInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Generator\Writer\Writable;
use Tardigrades\SectionField\Service\FieldManagerInterface;
use Tardigrades\SectionField\Service\FieldTypeManagerInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;

abstract class Generator implements GeneratorInterface
{
    /** @var FieldManagerInterface */
    protected $fieldManager;

    /** @var FieldTypeManagerInterface */
    protected $fieldTypeManager;

    /** @var SectionManagerInterface */
    protected $sectionManager;

    /** @var array */
    protected $relationships;

    /** @var array */
    protected $buildMessages = [];

    /** @var ContainerInterface */
    protected $container;

    public function __construct(
        FieldManagerInterface $fieldManager,
        FieldTypeManagerInterface $fieldTypeManager,
        SectionManagerInterface $sectionManager,
        ContainerInterface $container
    ) {
        $this->fieldManager = $fieldManager;
        $this->fieldTypeManager = $fieldTypeManager;
        $this->sectionManager = $sectionManager;
        $this->container = $container;

        $this->relationships = [];
    }

    protected function addOpposingRelationships(SectionInterface $section, array $fields): array
    {
        $this->relationships = $this->sectionManager->getRelationshipsOfAll();
        foreach ($this->relationships[(string) $section->getHandle()] as $fieldHandle => $relationship) {
            if (false !== strpos($fieldHandle, '-opposite')) {
                $fieldHandle = str_replace('-opposite', '', $fieldHandle);

                $oppositeRelationshipField = new FieldEntity();
                $config = [
                    'field' => [
                        'name' => $fieldHandle,
                        'handle' => $fieldHandle,
                        'kind' => $relationship['kind'],
                        'to' => $relationship['to']
                    ]
                ];

                if (!empty($relationship['relationship-type'])) {
                    $config['field']['relationship-type'] = $relationship['relationship-type'];
                }
                $oppositeRelationshipField->setConfig($config);
                $oppositeRelationshipFieldType = $this->fieldTypeManager
                    ->readByFullyQualifiedClassName(
                        $relationship['fullyQualifiedClassName']
                    );
                $oppositeRelationshipField->setFieldType($oppositeRelationshipFieldType);
                $fields[] = $oppositeRelationshipField;
            }
        }

        return $fields;
    }

    protected function getFieldTypeGeneratorConfig(FieldInterface $field, string $generateFor): array
    {
        $fieldType = $this->container->get((string) $field->getFieldType()->getFullyQualifiedClassName());
        $fieldTypeGeneratorConfig = $fieldType->getFieldTypeGeneratorConfig()->toArray();

        try {
            Assertion::notEmpty(
                $fieldTypeGeneratorConfig,
                'No generator defined for ' .
                $field->getName() . 'type: ' . $field->getFieldType()->getType()
            );

            Assertion::keyExists(
                $fieldTypeGeneratorConfig,
                $generateFor,
                'Nothing to do for this generator: ' . $generateFor
            );
        } catch (\Exception $exception) {
            $this->buildMessages[] = $exception->getMessage();
        }

        return $fieldTypeGeneratorConfig;
    }

    public function getBuildMessages(): array
    {
        return $this->buildMessages;
    }

    abstract public function generateBySection(SectionInterface $section): Writable;
}
