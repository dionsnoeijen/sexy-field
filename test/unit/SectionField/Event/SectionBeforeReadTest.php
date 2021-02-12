<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Event;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\Service\OptionsInterface;
use Tardigrades\SectionField\ValueObject\SectionConfig;

/**
 * @coversDefaultClass Tardigrades\SectionField\Event\SectionEntryBeforeRead
 * @covers ::<private>
 * @covers ::__construct
 */
final class SectionEntryBeforeReadTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var SectionEntryBeforeRead */
    private $sectionBeforeRead;

    /** @var \ArrayIterator|Mockery\MockInterface */
    private $data;

    /** @var OptionsInterface|Mockery\MockInterface */
    private $readOptions;

    /** @var SectionConfig|Mockery\MockInterface */
    private $sectionConfig;

    public function setUp(): void
    {
        $sectionConfigArray = [
            'section' => [
                'name' => 'foo',
                'handle' => 'bar',
                'fields' => [],
                'default' => 'Default',
                'namespace' => 'My\\Namespace'
            ]
        ];

        $this->data = Mockery::mock(\ArrayIterator::class);
        $this->readOptions = Mockery::mock(OptionsInterface::class);
        $this->sectionConfig = SectionConfig::fromArray($sectionConfigArray);
        $this->sectionBeforeRead = new SectionEntryBeforeRead(
            $this->data,
            $this->readOptions,
            $this->sectionConfig
        );
    }

    /**
     * @test
     * @covers ::getData
     */
    public function it_should_return_the_data()
    {
        $result = $this->sectionBeforeRead->getData();

        $this->assertEquals($this->data, $result);
    }

    /**
     * @test
     * @covers ::getReadOptions
     */
    public function it_should_return_the_read_options()
    {
        $result = $this->sectionBeforeRead->getReadOptions();

        $this->assertEquals($this->readOptions, $result);
    }

    /**
     * @test
     * @covers ::getSectionConfig
     */
    public function it_should_return_the_section_config()
    {
        $result = $this->sectionBeforeRead->getSectionConfig();

        $this->assertEquals($this->sectionConfig, $result);
    }
}
