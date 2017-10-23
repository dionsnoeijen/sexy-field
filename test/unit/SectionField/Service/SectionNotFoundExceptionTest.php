<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\SectionNotFoundException
 */
final class SectionNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_custom_message()
    {
        $exception = new SectionNotFoundException('custom message');
        $this->assertSame('custom message', $exception->getMessage());
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_default_message()
    {
        $exception = new SectionNotFoundException();
        $this->assertSame('Section not found', $exception->getMessage());
    }
}
