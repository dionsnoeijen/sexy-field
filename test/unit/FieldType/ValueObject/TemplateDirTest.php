<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\FieldType\ValueObject\TemplateDir
 * @covers ::<private>
 * @covers ::__construct
 */
class TemplateDirTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     * @covers ::__toString
     */
    public function it_should_create()
    {
        $template = TemplateDir::fromString('wheeee');
        $this->assertInstanceOf(TemplateDir::class, $template);
        $this->assertSame('wheeee', (string) $template);
    }
}
