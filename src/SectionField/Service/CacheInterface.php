<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;

interface CacheInterface
{

    /**
     * Start a cache item, it can be called even if it's not enabled
     *
     * Typical use:
     *
     * 1: Initialize: ->item(...)
     * 2: Is hit?: ->isHit()
     *      Yes: ->get()
     *      No: ->set()
     *
     * The section creator (CreateSection) contains a cache breaker.
     *
     * @param FullyQualifiedClassName $fullyQualifiedClassName #make unique based on section
     * @param array|null $requestedFields #make unique with it's requested fields
     * @param string|null $context #make unique with it's calling context
     * @param string|null $id #make unique for specific entry
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function start(
        FullyQualifiedClassName $fullyQualifiedClassName,
        array $requestedFields = null,
        string $context = null,
        string $id = null
    ): void;

    /**
     * Set data with tags for cache breaking
     *
     * Tagged with:
     *   - Section
     *   - Fields
     *   - Context
     *   - Related sections (so cache will be broken whenever one of this sections relationships is updated)
     *
     * @param array $data
     */
    public function set(array $data): void;

    /**
     * If disabled, just tell the caller no
     *
     * @return bool
     */
    public function isHit(): bool;

    /**
     * If disabled and implemented correctly, get is never called
     * because of an isHit check. If called anyway, just return empty data.
     *
     * @return array
     */
    public function get(): array;

    /**
     * Pass on a section (With it's fully qualified class name)
     * so all itmes tagged with this section will be invalidated
     *
     * @param FullyQualifiedClassName $fullyQualifiedClassName
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function invalidateForSection(FullyQualifiedClassName $fullyQualifiedClassName): void;

    /**
     * Pass on a context (Where the call came from, for example: ApiInfoCall)
     * so all items tagged with this context will be invalidated
     *
     * @param string $context
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function invalidateForContext(string $context): void;
}
