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

namespace Tardigrades\SectionField\Generator;

class XmlFormatter
{
    public static function format(string $string): string
    {
        $dom = new \DOMDocument();
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($string);
        return $dom->saveXML();
    }
}
