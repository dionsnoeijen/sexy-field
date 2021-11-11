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

namespace Tardigrades\Helper;

class StringConverter
{
    public static function toCamelCase(string $string, array $noStrip = []): string
    {
        $string = preg_replace('/[^a-z0-9' . implode('', $noStrip) . ']+/i', ' ', $string);
        $string = trim($string);
        $string = ucwords($string);
        $string = str_replace(" ", "", $string);
        $string = lcfirst($string);

        return $string;
    }

    public static function toSnakeCase(string $string): string
    {
        return strtolower(preg_replace(
            '/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', '_', $string));
    }

    public static function toSlug(string $string): string
    {
        $string = preg_replace('~[^\pL\d]+~u', '-', $string);
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
        $string = preg_replace('~[^-\w]+~', '', $string);
        $string = trim($string, '-');
        $string = preg_replace('~-+~', '-', $string);
        $string = strtolower($string);

        if (empty($string)) {
            return 'n-a';
        }

        return $string;
    }
}
