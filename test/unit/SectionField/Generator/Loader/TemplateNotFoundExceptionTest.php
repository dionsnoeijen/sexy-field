<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator\Loader;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\Generator\Loader\TemplateNotFoundException
 */
final class TemplateNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_custom_message()
    {
        $exception = new TemplateNotFoundException('custom message');
        $this->assertSame('custom message', $exception->getMessage());
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_default_message()
    {
        $exception = new TemplateNotFoundException();
        $this->assertSame('Template not found', $exception->getMessage());
    }
}
