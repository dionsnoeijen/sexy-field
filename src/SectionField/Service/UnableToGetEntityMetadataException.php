<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tardigrades\SectionField\Service;

class UnableToGetEntityMetadataException extends \Exception
{
    public function __construct($message = "", $code = 404, \Throwable $previous = null)
    {
        $message = !empty($message) ? $message : 'This entity does not seem to have metadata (::FIELDS)';

        parent::__construct($message, $code, $previous);
    }
}
