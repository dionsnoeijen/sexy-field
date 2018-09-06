<?php

declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;

/**
 * Class DefaultCache
 * @package Tardigrades\SectionField\Service
 */
class DefaultCache implements CacheInterface
{
    /** @var AdapterInterface */
    protected $cache;

    /** @var bool */
    protected $enabled;

    /** @var CacheItemInterface */
    private $item;

    /** @var string */
    private $sectionHandle;

    /** @var string */
    private $fieldKey;

    /** @var string */
    private $context;
    
    /** @var string[] */
    private $relationships;

    /**
     * DefaultCache constructor.
     *
     * @param TagAwareAdapterInterface $cache
     * @param bool $enabled
     */
    public function __construct(
        TagAwareAdapterInterface $cache,
        string $enabled = 'false'
    ) {
        $this->cache = $cache;
        $this->enabled = $enabled === 'false' ? false : true ;
        $this->item = null;
        $this->sectionHandle = null;
        $this->fieldKey = null;
        $this->relationships = null;
    }

    /**
     * {@inheritdoc}
     */
    public function start(
        FullyQualifiedClassName $fullyQualifiedClassName,
        array $requestedFields = null,
        string $context = null,
        string $id = null
    ): void {
        if ($this->enabled) {
            try {
                $this->item = $this->cache->getItem(
                    $this->getItemKey(
                        (string)$fullyQualifiedClassName,
                        $requestedFields,
                        $context,
                        $id
                    )
                );
                $this->context = sha1($context);
                $this->sectionHandle = sha1((string)$fullyQualifiedClassName);
                $this->fieldKey = $this->getFieldKey($requestedFields);
                $this->relationships = $this->getRelationships($fullyQualifiedClassName);
            } catch (\Exception $exception) {
                $this->enabled = false;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set(array $data): void
    {
        if ($this->enabled) {
            $this->item->set($data);
            $this->item->tag($this->sectionHandle);
            $this->item->tag($this->fieldKey);
            $this->item->tag($this->context);
            foreach ($this->relationships as $relationship) {
                $this->item->tag(sha1($relationship));
            }
            $this->cache->save($this->item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isHit(): bool
    {
        if (!$this->enabled) {
            return false;
        }
        return $this->item->isHit();
    }

    /**
     * {@inheritdoc}
     */
    public function get(): array
    {
        if (!$this->enabled) {
            return [];
        }
        return $this->item->get();
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateForSection(FullyQualifiedClassName $fullyQualifiedClassName): void
    {
        if ($this->enabled) {
            $this->cache->invalidateTags([sha1((string)$fullyQualifiedClassName)]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateForContext(string $context): void
    {
        if ($this->enabled) {
            $this->cache->invalidateTags([sha1($context)]);
        }
    }

    /**
     * Get the key for the item so the key will be unique for this
     * specific cache item and can't be unexpectedly overridden.
     *
     * @param string $sectionHandle
     * @param array $requestedFields
     * @param string|null $context
     * @param string|null $id
     * @return string
     */
    private function getItemKey(
        string $sectionHandle,
        array $requestedFields = null,
        string $context = null,
        string $id = null
    ): string {
        return sha1($sectionHandle) .
            $this->getFieldKey($requestedFields) .
            $this->getContextKey($context) .
            $this->getIdKey($id);
    }

    /**
     * Make sure the context is as a key and sha1.
     * In some cases the context could be a fully qualified class name
     * and therefore contain invalid characters for use in a key
     *
     * @param string|null $context
     * @return string
     */
    private function getContextKey(string $context = null): string
    {
        return !is_null($context) ? ('.' . sha1($context)) : '.no-context';
    }

    /**
     * A lot of calls contain the fields one want's to have in return.
     * Make sure this is also added to the item key.
     *
     * @param array $requestedFields
     * @return string
     */
    private function getFieldKey(array $requestedFields = null): string
    {
        if (is_null($requestedFields)) {
            return 'no-field-key';
        }
        return $fieldKey = '.' . sha1(implode(',', $requestedFields));
    }

    /**
     * Cache a specific entry? Add the id.
     *
     * @param string|null $id
     * @return string
     */
    private function getIdKey(string $id = null): string
    {
        return !is_null($id) ? ('.' . $id) : '.no-id';
    }

    /**
     * Entries can have relationships, make sure to tag them.
     *
     * @param FullyQualifiedClassName $fullyQualifiedClassName
     * @return array
     * @throws UnableToGetEntityMetadataException
     */
    private function getRelationships(FullyQualifiedClassName $fullyQualifiedClassName): array
    {
        try {
            $fields = (string)$fullyQualifiedClassName;
            $relationships = [];
            foreach ($fields::FIELDS as $field) {
                try {
                    if (!is_null($field['relationship']['class'])) {
                        $relationships[] = $field['relationship']['class'];
                    }
                } catch (\Exception $exception) {
                    // Just go on
                }
            }
            return $relationships;
        } catch (\Exception $exception) {
            throw new UnableToGetEntityMetadataException(
                'Cannot get ::FIELDS' . $exception->getMessage()
            );
        }
    }
}
