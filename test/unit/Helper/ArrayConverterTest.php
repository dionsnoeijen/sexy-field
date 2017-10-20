<?php
declare (strict_types = 1);

namespace Tardigrades\Helper;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\Helper\ArrayConverter
 */
final class ArrayConverterTest extends TestCase
{
    /**
     * @test
     * @covers ::recursive
     */
    public function it_converts_an_array_to_a_formatted_string()
    {
        $array = [
            'name' => 'Project',
            'handle' => 'project',
            'fields' => [
                'projectName',
                'projectSlug',
                'created',
                'updated'
            ],
            'slug' => 'projectSlug'
        ];

        $result = ArrayConverter::recursive($array);

        $expected = <<<EOT
name:Project
handle:project
fields:
- 0:projectName
- 1:projectSlug
- 2:created
- 3:updated
slug:projectSlug

EOT;

        $this->assertSame($expected, $result);
    }
}
