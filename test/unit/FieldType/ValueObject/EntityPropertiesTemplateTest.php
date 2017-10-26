<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\FieldType\ValueObject\EntityPropertiesTemplate
 * @covers ::<private>
 * @covers ::__construct
 */
class EntityPropertiesTemplateTest extends TestCase
{
    /**
     * @test
     * @covers ::create
     * @covers ::__toString
     */
    public function it_should_create()
    {
        $template = EntityPropertiesTemplate::create('wheeee');
        $this->assertInstanceOf(EntityPropertiesTemplate::class, $template);
        $this->assertSame('wheeee', (string) $template);
    }
}
