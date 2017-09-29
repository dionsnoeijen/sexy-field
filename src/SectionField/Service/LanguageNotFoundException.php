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

namespace Tardigrades\SectionField\Service;

class LanguageNotFoundException extends \Exception
{
    public function __construct($message = '', $code = 0, \Throwable $previous = null)
    {
        $message = !empty($message) ? $message : "Language not found.";

        parent::__construct($message, $code, $previous);
    }
}
