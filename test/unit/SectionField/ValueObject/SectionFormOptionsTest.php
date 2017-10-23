<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\SectionFormOptions
 * @covers ::<private>
 * @covers ::__construct
 */
class SectionFormOptionsTest extends TestCase
{
    /**
     * @test
     * @covers ::fromArray
     */
    public function it_creates_from_array()
    {
        $this->assertInstanceOf(SectionFormOptions::class, SectionFormOptions::fromArray(['a' => 'b']));
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getId
     * @covers ::getSlug
     * @covers ::getRedirect
     */
    public function it_gets_fields_if_well_defined_and_present()
    {
        $array = [
            'id' => 1,
            'slug' => 'snail',
            'redirect' => 'where?'
        ];
        $formOptions = SectionFormOptions::fromArray($array);
        $this->assertEquals($formOptions->getId(), Id::fromInt(1));
        $this->assertEquals($formOptions->getSlug(), Slug::fromString('snail'));
        $this->assertSame($formOptions->getRedirect(),'where?');
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getId
     */
    public function it_should_throw_exception_if_id_is_not_set()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The id is not set');
        $options = SectionFormOptions::fromArray(['slug' => 'snail', 'redirect' => 'where?']);
        $options->getId();
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getId
     */
    public function it_should_throw_exception_if_id_is_not_integer()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The id must be a digit');
        $options = SectionFormOptions::fromArray(['id' => 'one', 'slug' => 'snail', 'redirect' => 'where?']);
        $options->getId();
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getRedirect
     */
    public function it_should_throw_exception_if_redirect_is_not_set()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The redirect is not set');
        $options = SectionFormOptions::fromArray(['slug' => 'snail', 'id' => 43]);
        $options->getRedirect();
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getRedirect
     */
    public function it_should_throw_exception_if_redirect_is_not_string()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The redirect must be a string');
        $options = SectionFormOptions::fromArray(['id' => 900909009, 'slug' => 'snail', 'redirect' => ['where?']]);
        $options->getRedirect();
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getSlug
     */
    public function it_should_throw_exception_if_slug_is_not_set()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The slug is not set');
        $options = SectionFormOptions::fromArray(['redirect' => 'snail', 'id' => 43]);
        $options->getSlug();
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getSlug
     */
    public function it_should_throw_exception_if_slug_is_not_string()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The slug must be a string');
        $options = SectionFormOptions::fromArray(['id' => 900909009, 'slug' => ['snail'], 'redirect' => 'where?']);
        $options->getSlug();
    }

    /**
     * @test
     * @covers ::fromArray
     * @covers ::getSlug
     */
    public function it_should_throw_exception_if_slug_is_empty()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The slug is empty');
        $options = SectionFormOptions::fromArray(['id' => 41, 'slug' => '', 'redirect' => 'where?']);
        $options->getSlug();
    }
}
