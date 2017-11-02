<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\Entity\Section;
use Tardigrades\SectionField\Generator\Writer\Writable;

/**
 * @coversDefaultClass Tardigrades\SectionField\Generator\Generators
 * @covers ::__construct
 */
final class GeneratorsTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var GeneratorInterface|Mockery\MockInterface */
    private $generators;

    /** @var Generators */
    private $fixture;

    public function setUp()
    {
        $this->generators = [Mockery::mock(GeneratorInterface::class)];
        $this->fixture = new Generators($this->generators);
    }

    /**
     * @test
     * @covers ::generateBySection
     * @covers ::getBuildMessages
     */
    public function it_should_generate_by_section()
    {
        $section = new Section();
        $writable = Writable::create('template', 'namespace', 'filename');
        $this->generators[0]->shouldReceive('generateBySection')->once()->andReturn($writable);
        $this->generators[0]->shouldReceive('getBuildMessages')->once()->andReturn(['message']);
        $result = $this->fixture->generateBySection($section);

        $this->assertSame([$writable], $result);
        $this->assertSame(['message'], $this->fixture->getBuildMessages());
    }

    /**
     * @test
     * @covers ::generateBySection
     * @covers ::getBuildMessages
     */
    public function it_should_set_build_messages_when_writable_cannot_be_generated()
    {
        $section = new Section();
        $this->generators[0]->shouldReceive('generateBySection')->once()->andThrow(\Exception::class, 'error');
        $this->generators[0]->shouldReceive('getBuildMessages')->once()->andReturn(['message']);
        $result = $this->fixture->generateBySection($section);

        $this->assertSame([], $result);
        $this->assertSame(['error', 'message'], $this->fixture->getBuildMessages());
    }
}
