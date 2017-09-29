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

class ArrayConverter
{
    private static $value = '';

    public static function recursive(array $array, int $level = 1): string
    {
        if ($level === 1) {
            self::$value = '';
        }

        foreach($array as $key => $value) {
            if(is_array($value)) {
                self::$value .=
                    str_repeat('-', $level - 1) .
                    (($level-1 > 0 ) ? ' ' : '') .
                    $key . ':' . PHP_EOL;
                self::recursive($value, $level + 1);
            } else {
                self::$value .=
                    str_repeat('-', $level - 1) .
                    (($level-1 > 0 ) ? ' ' : '') .
                    $key . ':' . $value . PHP_EOL;
            }
        }

        return self::$value;
    }
}
