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
use Tardigrades\SectionField\Event\SectionEntryBeforeCreate;
use Tardigrades\SectionField\Event\SectionEntryCreated;
use Tardigrades\SectionField\Generator\CommonSectionInterface;

/**
 * {@inheritdoc}
 */
class CreateSection implements CreateSectionInterface
{
    /** @var CreateSectionInterface[] */
    private $creators;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(array $creators, EventDispatcherInterface $dispatcher)
    {
        $this->creators = $creators;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CommonSectionInterface $sectionEntryEntity, array $jitRelationships = null)
    {
        $this->dispatcher->dispatch(
            SectionEntryBeforeCreate::NAME,
            new SectionEntryBeforeCreate($sectionEntryEntity)
        );

        $update = !empty($sectionEntryEntity->getId());

        /** @var CreateSectionInterface $writer */
        foreach ($this->creators as $writer) {
            $writer->save($sectionEntryEntity, $jitRelationships);
        }

        $this->dispatcher->dispatch(
            SectionEntryCreated::NAME,
            new SectionEntryCreated($sectionEntryEntity, $update)
        );
    }
}
