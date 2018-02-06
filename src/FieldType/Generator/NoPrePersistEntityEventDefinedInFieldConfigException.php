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

namespace Tardigrades\FieldType\Generator;

use Throwable;

class NoPrePersistEntityEventDefinedInFieldConfigException extends \Exception
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        // @codingStandardsIgnoreLine
        $message = empty($message) ? 'In the field config this key: generator - entity - event with this value: - prePersist is not defined. Skipping pre persist rendering for this field.' : $message;

        parent::__construct($message, $code, $previous);
    }
}
