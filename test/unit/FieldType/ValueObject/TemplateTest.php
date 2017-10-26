<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\FieldType\ValueObject\Template
 * @covers ::<private>
 * @covers ::__construct
 */
class TemplateTest extends TestCase
{
    /**
     * @test
     * @covers ::create
     * @covers ::__toString
     */
    public function it_should_create()
    {
        $template = Template::create('wheeee');
        $this->assertInstanceOf(Template::class, $template);
        $this->assertSame('wheeee', (string) $template);
    }
}
