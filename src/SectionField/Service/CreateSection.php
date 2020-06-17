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
use Tardigrades\SectionField\Event\BeforeCreateAbortedException;
use Tardigrades\SectionField\Event\BeforeUpdateAbortedException;
use Tardigrades\SectionField\Event\SectionEntryBeforeCreate;
use Tardigrades\SectionField\Event\SectionEntryBeforeUpdate;
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

    /** @var string[] */
    private $invalidatedCaches = [];

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
     * @inheritDoc
     */
    public function save(
        CommonSectionInterface $sectionEntryEntity,
        ?OptionsInterface $createOptions = null
    ) {
        $update = !empty($sectionEntryEntity->getId());
        $this->beforeEvent(
            $sectionEntryEntity,
            $update,
            $createOptions
        );

        /** @var CreateSectionInterface $creator */
        foreach ($this->creators as $creator) {
            $creator->save($sectionEntryEntity, $createOptions);
        }

        try {
            $this->cache->invalidateForSection(
                FullyQualifiedClassName::fromString(get_class($sectionEntryEntity))
            );
        } catch (\Psr\Cache\InvalidArgumentException $exception) {
            //
        }

        $this->dispatcher->dispatch(
            new SectionEntryCreated(
                $sectionEntryEntity,
                $update,
                $createOptions
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function persist(
        CommonSectionInterface $sectionEntryEntity,
        ?OptionsInterface $createOptions = null
    ) {
        $update = !empty($sectionEntryEntity->getId());
        $this->beforeEvent(
            $sectionEntryEntity,
            $update,
            $createOptions
        );

        /** @var CreateSectionInterface $writer */
        foreach ($this->creators as $writer) {
            $writer->persist($sectionEntryEntity);
        }

        $class = get_class($sectionEntryEntity);
        if (!in_array($class, $this->invalidatedCaches)) {
            $this->invalidatedCaches[] = $class;
        }
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

        foreach ($this->invalidatedCaches as $class) {
            try {
                $this->cache->invalidateForSection(
                    FullyQualifiedClassName::fromString($class)
                );
            } catch (\Psr\Cache\InvalidArgumentException $exception) {
                //
            }
        }
        $this->invalidatedCaches = [];
    }

    /**
     * @param CommonSectionInterface $sectionEntryEntity
     * @param bool $update
     * @param OptionsInterface $createOptions
     * @throws BeforeCreateAbortedException
     * @throws BeforeUpdateAbortedException
     */
    private function beforeEvent(
        CommonSectionInterface $sectionEntryEntity,
        bool $update,
        ?OptionsInterface $createOptions = null
    ): void {
        if ($update) {
            $sectionEntryBeforeUpdate = new SectionEntryBeforeUpdate(
                $sectionEntryEntity,
                $createOptions
            );
            $this->dispatcher->dispatch($sectionEntryBeforeUpdate);
            if ($sectionEntryBeforeUpdate->aborted()) {
                throw new BeforeUpdateAbortedException();
            }
        } else {
            $sectionEntryBeforeCreate = new SectionEntryBeforeCreate(
                $sectionEntryEntity,
                $createOptions
            );
            $this->dispatcher->dispatch($sectionEntryBeforeCreate);
            if ($sectionEntryBeforeCreate->aborted()) {
                throw new BeforeCreateAbortedException();
            }
        }
    }
}
