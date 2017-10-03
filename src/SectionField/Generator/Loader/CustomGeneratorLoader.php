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

namespace Tardigrades\SectionField\Generator\Loader;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\FieldType\NoCustomGeneratorDefinedException;

class CustomGeneratorLoader
{
    public static function load(Field $field)
    {
        $segments = explode('\\', (string) $field->getFieldType()->getFullyQualifiedClassName());
        array_pop($segments);
        $segments = implode('\\', $segments);

        $fullyQualifiedClassName =
            (string) $segments .
            '\\Generators\\' .
            (string) $field->getFieldType()->getType() . 'Generator';

        if (!class_exists($fullyQualifiedClassName)) {
            throw new NoCustomGeneratorDefinedException();
        }

        $fieldInstance = new $fullyQualifiedClassName();

        return $fieldInstance;
    }
}
