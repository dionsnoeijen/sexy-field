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
use Tardigrades\SectionField\Event\BeforeDeleteAbortedException;
use Tardigrades\SectionField\Event\SectionEntryBeforeDelete;
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

    public function __construct(
        array $deleters,
        EventDispatcherInterface $dispatcher
    ) {
        $this->deleters = $deleters;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritdoc
     */
    public function delete(
        CommonSectionInterface $sectionEntryEntity,
        ?OptionsInterface $deleteOptions = null
    ): bool
    {
        $sectionEntryBeforeDelete = new SectionEntryBeforeDelete(
            $sectionEntryEntity,
            $deleteOptions
        );
        $this->dispatcher->dispatch($sectionEntryBeforeDelete);
        if ($sectionEntryBeforeDelete->aborted()) {
            throw new BeforeDeleteAbortedException();
        }

        $success = true;
        /** @var DeleteSectionInterface $deleter */
        foreach ($this->deleters as $deleter) {
            if (!$deleter->delete($sectionEntryEntity, $deleteOptions) && $success) {
                $success = false;
            }
        }

        $this->dispatcher->dispatch(
            new SectionEntryDeleted(
                $sectionEntryEntity,
                $success,
                $deleteOptions
            )
        );

        return $success;
    }

    /**
     * @inheritDoc
     */
    public function remove(
        CommonSectionInterface $sectionEntryEntity,
        ?OptionsInterface $deleteOptions = null
    ): void {
        $sectionEntryBeforeDelete = new SectionEntryBeforeDelete(
            $sectionEntryEntity,
            $deleteOptions
        );
        $this->dispatcher->dispatch($sectionEntryBeforeDelete);
        if ($sectionEntryBeforeDelete->aborted()) {
            throw new BeforeDeleteAbortedException();
        }

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
