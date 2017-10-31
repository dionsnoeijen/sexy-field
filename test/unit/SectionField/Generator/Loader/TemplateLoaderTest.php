<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator\Loader;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\Generator\Loader\TemplateLoader
 */
final class TemplateLoaderTest extends TestCase
{
    public function setUp()
    {
        vfsStream::setup('home');
    }

    /**
     * @test
     * @covers ::load
     */
    public function it_should_load_a_template_when_extension_is_not_php()
    {
        $file = vfsStream::url('home/doctrine.config.xml.template');

        $contents = 'the contents of the file';

        file_put_contents($file, $contents);

        $result = TemplateLoader::load('vfs://home/doctrine.config.xml.template');

        $this->assertSame($contents, $result);
    }

    /**
     * @test
     * @covers ::load
     */
    public function it_should_load_a_template_when_extension_is_php()
    {
        $file = vfsStream::url('home/doctrine.onetooone.xml.php');
        $contents = <<<TXT
<?php 
    echo \$type . PHP_EOL; 
    echo \$toPluralHandle . PHP_EOL; 
    echo \$toFullyQualifiedClassName . PHP_EOL; 
    echo \$fromHandle . PHP_EOL; 
    echo \$fromPluralHandle . PHP_EOL; 
    echo \$fromFullyQualifiedClassName . PHP_EOL; 
    echo \$toHandle; 
 ?>

TXT;

        $variables = [
            'type' => 'type',
            'toPluralHandle' => 'toPluralHandle',
            'toFullyQualifiedClassName' => 'fqcn',
            'fromHandle' => 'fromHandle',
            'fromPluralHandle' => 'fromPluralHandle',
            'fromFullyQualifiedClassName' => 'fromFullyQualifiedClassName',
            'toHandle' => 'toHandle'
        ];

        $expected = <<<TXT
type
toPluralHandle
fqcn
fromHandle
fromPluralHandle
fromFullyQualifiedClassName
toHandle
TXT;


        file_put_contents($file, $contents);

        $result = TemplateLoader::load('vfs://home/doctrine.onetooone.xml.php', $variables);

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @covers ::load
     */
    public function it_should_throw_exception_when_template_not_found()
    {
        $this->expectException(TemplateNotFoundException::class);
        $this->expectExceptionMessage('vfs://home/doctrine.config.xml.template: template not found');
        TemplateLoader::load('vfs://home/doctrine.config.xml.template');
    }
}
