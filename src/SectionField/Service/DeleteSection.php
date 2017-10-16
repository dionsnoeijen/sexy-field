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

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tardigrades\SectionField\Event\SectionEntryDeleted;

class DeleteSection implements DeleteSectionInterface
{
    /** @var array */
    private $deleters;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * DeleteSection constructor.
     * @param array $deleters
     */
    public function __construct(array $deleters, EventDispatcherInterface $dispatcher)
    {
        $this->deleters = $deleters;
        $this->dispatcher = $dispatcher;
    }

    public function delete($sectionEntryEntity): bool
    {
        $success = true;
        /** @var DeleteSectionInterface $deleter */
        foreach ($this->deleters as $deleter) {
            if (!$deleter->delete($sectionEntryEntity) && $success) {
                $success = false;
            }
        }

        $this->dispatcher->dispatch(
            SectionEntryDeleted::NAME,
            new SectionEntryDeleted($sectionEntryEntity, $success)
        );

        return $success;
    }
}
