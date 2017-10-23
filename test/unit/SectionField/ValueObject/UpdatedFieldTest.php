<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\UpdatedField
 * @covers ::<private>
 * @covers ::__construct
 */
class UpdatedFieldTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     */
    public function it_creates_from_string()
    {
        $updatedField = UpdatedField::fromString('updated sexy field');
        $this->assertInstanceOf(UpdatedField::class, $updatedField);
        $this->assertSame('updated sexy field', (string) $updatedField);
    }

    /**
     * @test
     * @covers ::fromString
     */
    public function it_throws_exception_if_empty()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Value is not specified');
        UpdatedField::fromString('');
    }
}
