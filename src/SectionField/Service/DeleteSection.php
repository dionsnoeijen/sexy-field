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
use Tardigrades\SectionField\Generator\CommonSectionInterface;

/**
 * {@inheritdoc}
 */
class DeleteSection implements DeleteSectionInterface
{
    /** @var DeleteSectionInterface[] */
    private $deleters;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(array $deleters, EventDispatcherInterface $dispatcher)
    {
        $this->deleters = $deleters;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CommonSectionInterface $sectionEntryEntity): bool
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

    public function remove(CommonSectionInterface $sectionEntryEntity): void
    {
        foreach ($this->deleters as $deleter) {
            $deleter->remove($sectionEntryEntity);
        }
    }

    public function flush(): void
    {
        foreach ($this->deleters as $deleter) {
            $deleter->flush();
        }
    }
}
