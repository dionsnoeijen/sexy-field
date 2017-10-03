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

namespace Tardigrades\SectionField\Service;

class CreateSection implements CreateSectionInterface
{
    /** @var array */
    private $creators;

    public function __construct(array $creators)
    {
        $this->creators = $creators;
    }

    public function save($data, array $jitRelationships = null)
    {
        /** @var CreateSectionInterface $writer */
        foreach ($this->creators as $writer) {
            $writer->save($data, $jitRelationships);
        }
    }
}
