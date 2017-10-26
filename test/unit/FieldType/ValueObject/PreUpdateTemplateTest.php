<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\FieldType\ValueObject\PreUpdateTemplate
 * @covers ::<private>
 * @covers ::__construct
 */
class PreUpdateTemplateTest extends TestCase
{
    /**
     * @test
     * @covers ::create
     * @covers ::__toString
     */
    public function it_should_create()
    {
        $template = PreUpdateTemplate::create('wheeee');
        $this->assertInstanceOf(PreUpdateTemplate::class, $template);
        $this->assertSame('wheeee', (string) $template);
    }
}
