<?php
declare (strict_types=1);

namespace Tardigrades\FieldType;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\FieldType\NoCustomGeneratorDefinedException
 */
final class FieldTypeDoesNotExistExceptionTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_custom_message()
    {
        $exception = new NoCustomGeneratorDefinedException('custom message');
        $this->assertSame('custom message', $exception->getMessage());
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_default_message()
    {
        $exception = new NoCustomGeneratorDefinedException();
        $this->assertSame('For this field there is no custom generator, falling back to default handling', $exception->getMessage());
    }
}
