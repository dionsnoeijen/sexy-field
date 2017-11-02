<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\FieldValue
 * @covers ::<private>
 * @covers ::__construct
 */
class FieldValueTest extends TestCase
{
    /**
     * @test
     * @covers ::fromHandleAndValue
     * @covers ::toArray
     */
    public function it_should_create_from_handle_and_value()
    {
        $handle = Handle::fromString('sexy handle');
        $value = 'sexy value';
        $sexyFieldValue = FieldValue::fromHandleAndValue($handle, $value);
        $this->assertSame($sexyFieldValue->toArray(), [(string) $handle, $value]);
    }
}
