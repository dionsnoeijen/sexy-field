<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator\Writer;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\Generator\Writer\Writable
 * @covers ::__construct
 */
final class WritableTest extends TestCase
{
    /**
     * @test
     * @covers ::create
     * @covers ::getTemplate
     * @covers ::getNamespace
     * @covers ::getFilename
     */
    public function it_should_create_correctly()
    {
        $writable = Writable::create('template', 'namespace', 'filename');

        $this->assertSame('template', $writable->getTemplate());
        $this->assertSame('namespace', $writable->getNamespace());
        $this->assertSame('filename', $writable->getFilename());
    }
}
