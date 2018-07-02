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
        $this->assertSame('Sexyclass', $fullyQualifiedSexyClassName->getClassName());
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $sexyString = 'sexy.class';
        $fullyQualifiedSexyClassNameString = (string)FullyQualifiedClassName::fromString($sexyString);
        $this->assertSame('sexy\class', $fullyQualifiedSexyClassNameString);
    }

    /**
     * @test
     * @covers ::toHandle
     */
    public function it_converts_a_fully_qualified_class_name()
    {
        $classname = FullyQualifiedClassName::fromString('\\My\\Namespace\\Classname');
        $result = $classname->toHandle();

        $this->assertInstanceOf(Handle::class, $result);
        $this->assertSame('classname', (string) $result);
    }
}
