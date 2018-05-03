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

namespace Tardigrades\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\FieldTypeGeneratorConfig;

interface FieldTypeInterface
{
    public function setConfig(FieldConfig $fieldConfig): FieldTypeInterface;
    public function getConfig(): FieldConfig;
    public function getFieldTypeGeneratorConfig(): FieldTypeGeneratorConfig;

    /**
     * @param FormBuilderInterface $formBuilder
     * @param SectionInterface $section
     * @param CommonSectionInterface $sectionEntity
     * @param SectionManagerInterface $sectionManager
     * @param ReadSectionInterface $readSection
     * @param Request $request
     *
     * @return FormBuilderInterface
     */
    public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        CommonSectionInterface $sectionEntity,
        SectionManagerInterface $sectionManager,
        ReadSectionInterface $readSection,
        Request $request
    ): FormBuilderInterface;

    public function directory(): string;
}
