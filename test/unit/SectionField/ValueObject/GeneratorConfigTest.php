<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\GeneratorConfig
 * @covers ::<private>
 * @covers ::__construct
 */
class GeneratorConfigTest extends TestCase
{
    /**
     * @test
     * @covers ::fromArray
     * @covers ::toArray
     */
    public function it_creates_from_array()
    {
        $array = ['generator' =>
            ['sexyGenerator' => 'something']
        ];
        $sexyGeneratorConfig = GeneratorConfig::fromArray($array);
        $this->assertInstanceOf(GeneratorConfig::class, $sexyGeneratorConfig);
        $this->assertSame($sexyGeneratorConfig->toArray(), $array['generator']);

    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function in_throws_exception_if_key_does_not_exist()
    {
        $array = ['not generator' =>
            ['sexyGenerator' => 'something']
        ];
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Config is not a section config');
        GeneratorConfig::fromArray($array);
    }
}
