<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\Created
 * @covers ::<private>
 * @covers ::__construct
 */
class CreatedTest extends TestCase
{
    /**
     * @test
     * @covers ::fromDateTime
     * @covers ::getDateTime
     */
    public function it_should_create_from_DateTime()
    {
        $datetime = new \DateTime('2000-11-11T12:12:12');
        $created = Created::fromDateTime($datetime);
        $this->assertInstanceOf(Created::class, $created);
        $this->assertEquals($created->getDateTime(), new \DateTime('2000-11-11T12:12:12'));
        $this->assertEquals((string) $created, '2000-11-11T12:12');
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $datetime = new \DateTime();
        $afterString = (string)Created::fromDateTime($datetime);
        $dateString = $datetime->format('Y-m-d\TH:i');
        $this->assertSame($dateString, $afterString);
    }
}
