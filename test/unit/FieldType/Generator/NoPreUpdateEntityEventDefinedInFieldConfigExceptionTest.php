<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Generator;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\FieldType\Generator\NoPreUpdateEntityEventDefinedInFieldConfigException
 */
final class NoPreUpdateEntityEventDefinedInFieldConfigExceptionTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_custom_message()
    {
        $exception = new NoPreUpdateEntityEventDefinedInFieldConfigException('custom message');
        $this->assertSame('custom message', $exception->getMessage());
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function it_should_construct_with_default_message()
    {
        $exception = new NoPreUpdateEntityEventDefinedInFieldConfigException();
        $this->assertSame(
            'In the field config this key: entityEvents with this value: - preUpdate is not defined. Skipping pre update rendering for this field.',
            $exception->getMessage()
        );
    }
}
