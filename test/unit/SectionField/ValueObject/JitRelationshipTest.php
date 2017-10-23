<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\JitRelationship
 * @covers ::<private>
 * @covers ::__construct
 */
class JitRelationshipTest extends TestCase
{
    /**
     * @test
     * @covers ::getId
     * @covers ::getFullyQualifiedClassName
     * @covers ::fromFullyQualifiedClassNameAndId
     */
    public function it_should_create()
    {
        $fullClassName = FullyQualifiedClassName::fromString('Sexy');
        $id = Id::fromInt(42);
        $jitRelationship = JitRelationship::fromFullyQualifiedClassNameAndId($fullClassName, $id);
        $this->assertInstanceOf(JitRelationship::class, $jitRelationship);
        $this->assertSame($id, $jitRelationship->getId());
        $this->assertSame($fullClassName, $jitRelationship->getFullyQualifiedClassName());
    }
}
