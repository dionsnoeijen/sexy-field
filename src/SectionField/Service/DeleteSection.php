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

class DeleteSection implements DeleteSectionInterface
{
    /** @var array */
    private $deleters;

    /**
     * DeleteSection constructor.
     * @param array $deleters
     */
    public function __construct(array $deleters)
    {
        $this->deleters = $deleters;
    }

    public function delete($sectionEntryEntity): bool
    {
        /** @var DeleteSectionInterface $deleter */
        foreach ($this->deleters as $deleter) {
            $deleter->delete($sectionEntryEntity);
        }
    }
}
