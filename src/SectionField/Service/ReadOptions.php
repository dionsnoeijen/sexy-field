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

use Assert\Assertion;
use Assert\AssertionFailedException;
use Assert\InvalidArgumentException;
use PHPUnit\Framework\AssertionFailedError;
use Tardigrades\SectionField\ValueObject\Slug;
use Tardigrades\SectionField\ValueObject\After;
use Tardigrades\SectionField\ValueObject\Before;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Limit;
use Tardigrades\SectionField\ValueObject\Offset;
use Tardigrades\SectionField\ValueObject\OrderBy;
use Tardigrades\SectionField\ValueObject\Search;
use Tardigrades\SectionField\ValueObject\Sort;

class ReadOptions implements ReadOptionsInterface
{
    /** Read options that are relevant for all readers */
    const ID = 'id';
    const SLUG = 'slug';
    const SECTION = 'section';
    const SECTION_ID = 'sectionId';
    const LIMIT = 'limit';
    const OFFSET = 'offset';
    const ORDER_BY = 'orderBy';
    const SORT = 'sort';
    const BEFORE = 'before';
    const AFTER = 'after';
    const LOCALE_ENABLED = 'localeEnabled';
    const LOCALE = 'locale';
    const SEARCH = 'search';
    const FIELD = 'field';
    const JOIN = 'join';

    /**
     * @var string If you know in advance what fields you are going to need
     * use the fetchFields option. That way a custom DQL query will be built
     * and greatly improve performance because we are bypassing the default
     * LAZY_LOADING setup of doctrine.
     *
     * A string like that should be comma separated. If you need fields that are
     * in a relationship, add the entity property name that would return the relationship.
     * title,subTitle,someRelation,someRelationTitle
     */
    const FETCH_FIELDS = 'fetchFields';

    /** @var array */
    protected $options;

    private function __construct(
        array $options
    ) {
        $valid = false;
        if (is_array($options[ReadOptions::SECTION])) {
            $valid = true;
        }

        if (is_string($options[ReadOptions::SECTION])) {
            $valid = true;
        }

        if ($options[ReadOptions::SECTION] instanceof FullyQualifiedClassName) {
            $valid = true;
        }

        if (!$valid) {
            throw new InvalidArgumentException(
                'The section is not of a valid type',
                400,
                null,
                $options[ReadOptions::SECTION]
            );
        }

        $this->options = $options;
    }

    public function getSection(): array
    {
        $sectionEntities = [];

        if ($this->options[ReadOptions::SECTION] instanceof FullyQualifiedClassName) {
            $sectionEntities = [$this->options[ReadOptions::SECTION]];
        }

        if (is_string($this->options[ReadOptions::SECTION])) {
            $sectionEntities = [FullyQualifiedClassName::fromString($this->options[ReadOptions::SECTION])];
        }

        if (is_array($this->options[ReadOptions::SECTION])) {
            foreach ($this->options[ReadOptions::SECTION] as $section) {
                $sectionEntities[] = FullyQualifiedClassName::fromString((string) $section);
            }
        }

        return $sectionEntities;
    }

    public function getSectionId(): ?Id
    {
        try {
            Assertion::keyIsset(
                $this->options,
                ReadOptions::SECTION_ID,
                'The sectionId is not set'
            );
            Assertion::integerish(
                $this->options[ReadOptions::SECTION_ID],
                'The sectionId needs to be an integer'
            );
        } catch (AssertionFailedException $exception) {
            return null;
        }

        return Id::fromInt($this->options[ReadOptions::SECTION_ID]);
    }

    public function getOffset(): ?Offset
    {
        try {
            Assertion::keyIsset(
                $this->options,
                ReadOptions::OFFSET,
                'The offset is not set'
            );
            Assertion::integerish(
                $this->options[ReadOptions::OFFSET],
                'The offset needs to be an integer.'
            );
        } catch (AssertionFailedException $exception) {
            return null;
        }

        return Offset::fromInt($this->options[ReadOptions::OFFSET]);
    }

    public function getLimit(): ?Limit
    {
        try {
            Assertion::keyIsset(
                $this->options,
                ReadOptions::LIMIT,
                'The limit is not set'
            );
            Assertion::integerish(
                $this->options[ReadOptions::LIMIT],
                'The limit needs to be an integer.'
            );
        } catch (AssertionFailedException $exception) {
            return null;
        }

        return Limit::fromInt($this->options[ReadOptions::LIMIT]);
    }

    public function getOrderBy(): ?OrderBy
    {
        try {
            Assertion::keyIsset(
                $this->options,
                ReadOptions::ORDER_BY,
                'orderBy is not set'
            );
            Assertion::isArray(
                $this->options[ReadOptions::ORDER_BY],
                'Order by needs to be an array. Example: (["some" => "ASC"])'
            );
            $handle = Handle::fromString(key($this->options[ReadOptions::ORDER_BY]));
            $sort = Sort::fromString(array_values($this->options[ReadOptions::ORDER_BY])[0]);
            $orderBy = OrderBy::fromHandleAndSort($handle, $sort);
        } catch (AssertionFailedException $exception) {
            return null;
        }

        return $orderBy;
    }

    public function getBefore(): ?Before
    {
        try {
            Assertion::keyIsset($this->options, ReadOptions::BEFORE, 'Before is not defined');
            Assertion::string($this->options[ReadOptions::BEFORE]);
        } catch (AssertionFailedException $exception) {
            return null;
        }

        return Before::fromString($this->options[ReadOptions::BEFORE]);
    }

    public function getAfter(): ?After
    {
        try {
            Assertion::keyIsset($this->options, ReadOptions::AFTER, 'After is not defined');
            Assertion::string($this->options[ReadOptions::AFTER]);
        } catch (AssertionFailedException $exception) {
            return null;
        }

        return After::fromString($this->options[ReadOptions::AFTER]);
    }

    public function getLocaleEnabled(): ?bool
    {
        try {
            Assertion::keyIsset($this->options, ReadOptions::LOCALE_ENABLED, 'localeEnabled is not set');
            Assertion::boolean($this->options[ReadOptions::LOCALE_ENABLED]);
        } catch (AssertionFailedException $exception) {
            return null;
        }

        return (bool) $this->options[ReadOptions::LOCALE_ENABLED];
    }

    public function getLocale(): ?string
    {
        try {
            Assertion::keyIsset($this->options, ReadOptions::LOCALE, 'No locale defined');
            Assertion::string($this->options[ReadOptions::LOCALE], 'Locale is supposed to be a string like en_EN');
        } catch (AssertionFailedException $exception) {
            return null;
        }

        return (string) $this->options[ReadOptions::LOCALE];
    }

    public function getSearch(): ?Search
    {
        try {
            Assertion::keyIsset($this->options, ReadOptions::SEARCH, 'No search defined');
            Assertion::string($this->options[ReadOptions::SEARCH], 'The search term must be a string');
        } catch (AssertionFailedException $exception) {
            return null;
        }

        return Search::fromString($this->options[ReadOptions::SEARCH]);
    }

    public function getField(): ?array
    {
        try {
            Assertion::keyExists($this->options, ReadOptions::FIELD,
                'The key field should exist'
            );
            Assertion::notEmpty($this->options[ReadOptions::FIELD],
                'The field option must contain something.'
            );
            Assertion::isArray(
                $this->options[ReadOptions::FIELD],
                'The field option must be an array. "fieldHandle" => "value"'
            );
        } catch (AssertionFailedException $exception) {
            return null;
        }

        return $this->options[ReadOptions::FIELD];
    }

    public function getJoin(): ?array
    {
        try {
            Assertion::keyExists($this->options, ReadOptions::JOIN, 'The key join should exist');
            Assertion::notEmpty($this->options[ReadOptions::JOIN],
                'The join option must contain something.'
            );
            Assertion::isArray(
                $this->options[ReadOptions::JOIN],
                'The join option must be an array. "fieldHandle" => "value"'
            );
        } catch (AssertionFailedException $exception) {
            return null;
        }

        return $this->options[ReadOptions::JOIN];
    }

    public function getId(): ?Id
    {
        try {
            Assertion::keyIsset($this->options, ReadOptions::ID, 'This id is not set');
            Assertion::digit($this->options[ReadOptions::ID], 'The id is not numeric');
        } catch (AssertionFailedException $exception) {
            return null;
        }

        return Id::fromInt($this->options[ReadOptions::ID]);
    }

    public function getSlug(): ?Slug
    {
        try {
            Assertion::keyIsset($this->options, ReadOptions::SLUG, 'The slug is not set');

            // There is a possibility the read options are built with a value object,
            // added flexibility by converting value to slug first.
            if ($this->options[ReadOptions::SLUG] instanceof Slug) {
                Assertion::string((string) $this->options[ReadOptions::SLUG], 'The slug is supposed to be a string');
            } else {
                Assertion::string($this->options[ReadOptions::SLUG], 'The slug is supposed to be a string');
            }

            return Slug::fromString((string) $this->options[ReadOptions::SLUG]);
        } catch (AssertionFailedException $exception) {
            return null;
        }
    }

    public function getFetchFields(): ?array
    {
        try {
            Assertion::keyIsset($this->options, ReadOptions::FETCH_FIELDS, 'No fetch fields');
            Assertion::notEmpty($this->options[ReadOptions::FETCH_FIELDS],
                'The fetch fields are empty.'
            );
            Assertion::string($this->options[ReadOptions::FETCH_FIELDS], 'Fetch fields must be a string');
        } catch (AssertionFailedException $exception) {
            return null;
        }

        return explode(',', $this->options[ReadOptions::FETCH_FIELDS]);
    }

    public static function fromArray(array $options): ReadOptionsInterface
    {
        return new static($options);
    }

    public function toArray(): array
    {
        return $this->options;
    }
}
