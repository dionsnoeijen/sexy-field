<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types=1);

namespace Tardigrades\Helper;

use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Handle;

class FullyQualifiedClassNameConverter
{
    public static function toHandle(FullyQualifiedClassName $fullyQualifiedClassName): Handle
    {
        $handle = explode('\\', (string) $fullyQualifiedClassName);
        $handle = end($handle);
        return Handle::fromString(lcfirst($handle));
    }
}
