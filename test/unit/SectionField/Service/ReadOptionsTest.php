<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\ValueObject\After;
use Tardigrades\SectionField\ValueObject\Before;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Limit;
use Tardigrades\SectionField\ValueObject\Offset;
use Tardigrades\SectionField\ValueObject\OrderBy;
use Tardigrades\SectionField\ValueObject\Search;
use Tardigrades\SectionField\ValueObject\Slug;
use Tardigrades\SectionField\ValueObject\Sort;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\ReadOptions
 * @covers ::<private>
 * @covers ::__construct
 */
final class ReadOptionsTest extends TestCase
{
    /**
     * @test
     * @covers ::fromArray
     * @covers ::getId
     * @covers ::getSection
     * @covers ::getSlug
     * @covers ::getSectionId
     * @covers ::getLimit
     * @covers ::getOffset
     * @covers ::getOrderBy
     * @covers ::getBefore
     * @covers ::getAfter
     * @covers ::getLocaleEnabled
     * @covers ::getLocale
     * @covers ::getSearch
     * @covers ::getField
     * @covers ::toArray
     */
    public function it_should_create_from_array_when_section_is_array()
    {
        $date = new \DateTime('2017-10-21T15:03');
        $array = [
            'id' => 1,
            'slug' => 'section-one',
            'section' => ['Section One'],
            'sectionId' => 2,
            'limit' => 3,
            'offset' => 4,
            'orderBy' => ['some' => 'asc'],
            'before' => (string) Before::fromDateTime($date),
            'after' => (string) Before::fromDateTime($date),
            'localeEnabled' => true,
            'locale' => 'en_EN',
            'search' => 'search',
            'field' => ['color' => 'purple']
        ];
        $result = ReadOptions::fromArray($array);
        $this->assertEquals(Id::fromInt(1), $result->getId());
        $this->assertEquals(Slug::fromString('section-one'), $result->getSlug());
        $this->assertEquals(
            [
                FullyQualifiedClassName::fromString('Section One')
            ],
            $result->getSection()
        );
        $this->assertEquals(Id::fromInt(2), $result->getSectionId());
        $this->assertEquals(Limit::fromInt(3), $result->getLimit());
        $this->assertEquals(Offset::fromInt(4), $result->getOffset());
        $this->assertEquals(
            OrderBy::fromHandleAndSort(
                Handle::fromString('some'),
                Sort::fromString('asc')
            ),
            $result->getOrderBy()
        );
        $this->assertEquals(Before::fromDateTime($date), $result->getBefore());
        $this->assertEquals(After::fromDateTime($date), $result->getAfter());
        $this->assertEquals(true, $result->getLocaleEnabled());
        $this->assertEquals('en_EN', $result->getLocale());
        $this->assertEquals(Search::fromString('search'), $result->getSearch());
        $this->assertEquals(['color' => 'purple'], $result->getField());
        $this->assertEquals($array, $result->toArray());
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getId
     * @covers ::getSection
     * @covers ::getSlug
     * @covers ::getSectionId
     * @covers ::getLimit
     * @covers ::getOffset
     * @covers ::getOrderBy
     * @covers ::getBefore
     * @covers ::getAfter
     * @covers ::getLocaleEnabled
     * @covers ::getLocale
     * @covers ::getSearch
     * @covers ::getField
     */
    public function it_should_return_null_values_when_array_elements_are_wrong()
    {
        $array = [
            'id' => 'a',
            'slug' => [],
            'section' => ['Section One'],
            'sectionId' => [],
            'limit' => [],
            'offset' => [],
            'orderBy' => 1,
            'before' => [],
            'after' => [],
            'localeEnabled' => [],
            'locale' => [],
            'search' => [],
            'field' => 1
        ];
        $result = ReadOptions::fromArray($array);
        $this->assertEquals(null, $result->getId());
        $this->assertEquals(null, $result->getSlug());
        $this->assertEquals(
            [
                FullyQualifiedClassName::fromString('Section One')
            ],
            $result->getSection()
        );
        $this->assertEquals(null, $result->getSectionId());
        $this->assertEquals(null, $result->getLimit());
        $this->assertEquals(null, $result->getOffset());
        $this->assertEquals(null, $result->getOrderBy());
        $this->assertEquals(null, $result->getBefore());
        $this->assertEquals(null, $result->getAfter());
        $this->assertEquals(null, $result->getLocaleEnabled());
        $this->assertEquals(null, $result->getLocale());
        $this->assertEquals(null, $result->getSearch());
        $this->assertEquals(null, $result->getField());
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getSection
     * @covers ::getSlug
     */
    public function it_should_create_when_slug_is_value_object()
    {
        $array = [
            'slug' => Slug::fromString('slug'),
            'section' => ['Section One'],
        ];
        $result = ReadOptions::fromArray($array);
        $this->assertEquals(Slug::fromString('slug'), $result->getSlug());
        $this->assertEquals(
            [
                FullyQualifiedClassName::fromString('Section One')
            ],
            $result->getSection()
        );
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getSection
     */
    public function it_should_create_from_array_when_section_is_string()
    {
        $array = [
            'section' => 'bla'
        ];
        $result = ReadOptions::fromArray($array);
        $this->assertEquals([FullyQualifiedClassName::fromString('bla')], $result->getSection());
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getSection
     */
    public function it_should_create_from_array_when_section_is_fqcn()
    {
        $fqcn = FullyQualifiedClassName::fromString('\\My\\Namespace\\Classname');
        $array = [
            'section' => $fqcn
        ];
        $result = ReadOptions::fromArray($array);

        $this->assertEquals([$fqcn], $result->getSection());
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_section_key_is_invalid()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The section is not of a valid type');
        $array = [
            'section' => null
        ];

        ReadOptions::fromArray($array);
    }
}
