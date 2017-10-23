<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\FieldTypeNotFoundException
 */
final class FieldTypeNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_custom_message()
    {
        $exception = new FieldTypeNotFoundException('custom message');
        $this->assertSame('custom message', $exception->getMessage());
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_default_message()
    {
        $exception = new FieldTypeNotFoundException();
        $this->assertSame(
            'Field type not found, install the accompanying field type first.',
            $exception->getMessage()
        );
    }
}
