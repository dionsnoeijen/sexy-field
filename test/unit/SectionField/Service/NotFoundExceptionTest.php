<?php
declare(strict_types=1);

namespace Tardigrades\SectionField\Service;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\NotFoundException
 */
final class NotFoundExceptionTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_custom_message()
    {
        $this->assertSame('custom message', ((new NotFoundException('custom message'))->getMessage()));
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_default_message()
    {
        $this->assertSame('Not found', ((new NotFoundException)->getMessage()));
    }
}
