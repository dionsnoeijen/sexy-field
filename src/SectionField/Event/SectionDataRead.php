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

namespace Tardigrades\SectionField\Event;

use Symfony\Component\EventDispatcher\Event;

class SectionDataRead extends Event
{
    const NAME = 'section.data.read';

    /** @var \ArrayIterator */
    private $data;

    public function __construct(\ArrayIterator $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
