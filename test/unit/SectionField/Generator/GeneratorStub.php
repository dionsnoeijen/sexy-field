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

use Tardigrades\Entity\FieldInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Generator\Writer\Writable;

class GeneratorStub extends Generator
{
    public function generateBySection(SectionInterface $section): Writable
    {
        return Writable::create('template', 'namespace', 'filename');
    }

    public function newAddOpposingRelationships(SectionInterface $section, array $fields)
    {
        return $this->addOpposingRelationships($section, $fields);
    }

    public function newGetFieldTypeGeneratorConfig(FieldInterface $field, string $generateFor)
    {
        return $this->getFieldTypeGeneratorConfig($field, $generateFor);
    }

    public function newGetFieldTypeTemplateDirectory(FieldInterface $field, string $supportingDirectory)
    {
        return $this->getFieldTypeTemplateDirectory($field, $supportingDirectory);
    }
}
