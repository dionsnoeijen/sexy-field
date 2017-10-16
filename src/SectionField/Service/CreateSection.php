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
use Tardigrades\SectionField\Event\SectionEntryCreated;

class CreateSection implements CreateSectionInterface
{
    /** @var array */
    private $creators;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(array $creators, EventDispatcherInterface $dispatcher)
    {
        $this->creators = $creators;
        $this->dispatcher = $dispatcher;
    }

    public function save($sectionEntryEntity, array $jitRelationships = null)
    {
        /** @var CreateSectionInterface $writer */
        foreach ($this->creators as $writer) {
            $writer->save($sectionEntryEntity, $jitRelationships);
        }

        $this->dispatcher->dispatch(
            SectionEntryCreated::NAME,
            new SectionEntryCreated($sectionEntryEntity, true)
        );
    }
}
