<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\Generator\FieldTypeDoesNotExistException
 */
final class FieldTypeDoesNotExistExceptionTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_custom_message()
    {
        $exception = new FieldTypeDoesNotExistException('custom message');
        $this->assertSame('custom message', $exception->getMessage());
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_default_message()
    {
        $exception = new FieldTypeDoesNotExistException();
        $this->assertSame('Field type not found based on fully qualified class name', $exception->getMessage());
    }
}
