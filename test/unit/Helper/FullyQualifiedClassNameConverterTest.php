<?php
declare (strict_types = 1);

namespace Tardigrades\Helper;

use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Handle;

/**
 * @coversDefaultClass Tardigrades\Helper\FullyQualifiedClassNameConverter
 */
final class FullyQualifiedClassNameConverterTest extends TestCase
{
    /**
     * @test
     * @covers ::toHandle
     */
    public function it_converts_a_fully_qualified_class_name()
    {
        $classname = FullyQualifiedClassName::fromString('\\My\\Namespace\\Classname');
        $result = FullyQualifiedClassNameConverter::toHandle($classname);

        $this->assertInstanceOf(Handle::class, $result);
        $this->assertSame('classname', (string) $result);
    }
}
