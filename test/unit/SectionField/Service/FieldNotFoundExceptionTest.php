<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\FieldNotFoundException
 */
final class FieldNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_custom_message()
    {
        $exception = new FieldNotFoundException('custom message');
        $this->assertSame('custom message', $exception->getMessage());
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_default_message()
    {
        $exception = new FieldNotFoundException();
        $this->assertSame('Field not found', $exception->getMessage());
    }
}
