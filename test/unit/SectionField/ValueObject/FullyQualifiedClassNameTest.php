<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\FullyQualifiedClassName
 * @covers ::<private>
 * @covers ::__construct
 */
class FullyQualifiedClassNameTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     * @covers ::getClassName
     */
    public function it_should_create_from_string()
    {
        $sexyString = 'sexy.class';
        $fullyQualifiedSexyClassName = FullyQualifiedClassName::fromString($sexyString);
        $this->assertInstanceOf(FullyQualifiedClassName::class, $fullyQualifiedSexyClassName);
        $this->assertSame('class', $fullyQualifiedSexyClassName->getClassName());
    }

    /**
     * @test
     * @covers ::fromNamespaceAndClassName
     * @covers ::getClassName
     */
    public function it_should_create_from_namespace_and_classname()
    {
        $namespace = SectionNamespace::fromString('sexyspace');
        $classname = ClassName::fromString('Sexyclass');
        $fullyQualifiedSexyClassName = FullyQualifiedClassName::fromNamespaceAndClassName($namespace, $classname);
        $this->assertInstanceOf(FullyQualifiedClassName::class, $fullyQualifiedSexyClassName);
        $this->assertSame($fullyQualifiedSexyClassName->getClassName(), 'Sexyclass');
    }
}
