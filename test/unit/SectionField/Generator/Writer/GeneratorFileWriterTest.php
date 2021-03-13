<?php
declare (strict_types = 1);

namespace Tardigrades\SectionField\Generator\Writer;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\Generator\Writer\GeneratorFileWriter
 */
final class GeneratorFileWriterTest extends TestCase
{
    /**
     * @test
     * @covers ::getPsr4AutoloadDirectoryForNamespace
     */
    public function it_should_get_autoload_directory()
    {
        $this->assertStringEndsWith(
            str_replace('/', DIRECTORY_SEPARATOR, '/sexy-field/src'),
            GeneratorFileWriter::getPsr4AutoloadDirectoryForNamespace("Tardigrades")
        );
    }

    /**
     * @test
     * @covers ::getPsr4AutoloadDirectoryForNamespace
     */
    public function it_should_fail_with_a_nonexistent_namespace()
    {
        $this->expectException(\InvalidArgumentException::class);
        GeneratorFileWriter::getPsr4AutoloadDirectoryForNamespace("Not\A\Real\Namespace");
    }
}
