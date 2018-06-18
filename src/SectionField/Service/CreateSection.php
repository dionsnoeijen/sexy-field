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
use Tardigrades\SectionField\Api\Service\ApiCacheInterface;
use Tardigrades\SectionField\Event\SectionEntryBeforeCreate;
use Tardigrades\SectionField\Event\SectionEntryCreated;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;

/**
 * {@inheritdoc}
 */
class CreateSection implements CreateSectionInterface
{
    /** @var CreateSectionInterface[] */
    private $creators;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var CacheInterface */
    private $cache;

    public function __construct(
        array $creators,
        EventDispatcherInterface $dispatcher,
        CacheInterface $cache
    ) {
        $this->creators = $creators;
        $this->dispatcher = $dispatcher;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CommonSectionInterface $sectionEntryEntity)
    {
        $this->dispatcher->dispatch(
            SectionEntryBeforeCreate::NAME,
            new SectionEntryBeforeCreate($sectionEntryEntity)
        );

        $update = !empty($sectionEntryEntity->getId());

        /** @var CreateSectionInterface $writer */
        foreach ($this->creators as $writer) {
            $writer->save($sectionEntryEntity);
        }

        $this->cache->invalidateForSection(
            FullyQualifiedClassName::fromString(get_class($sectionEntryEntity))
        );

        $this->dispatcher->dispatch(
            SectionEntryCreated::NAME,
            new SectionEntryCreated($sectionEntryEntity, $update)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function persist(CommonSectionInterface $sectionEntryEntity)
    {
        /** @var CreateSectionInterface $writer */
        foreach ($this->creators as $writer) {
            $writer->persist($sectionEntryEntity);
        }

        $this->cache->invalidateForSection(
            FullyQualifiedClassName::fromString(get_class($sectionEntryEntity))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        /** @var CreateSectionInterface $writer */
        foreach ($this->creators as $writer) {
            $writer->flush();
        }
    }
}
