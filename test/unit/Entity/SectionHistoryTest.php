<?php
declare (strict_types = 1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\Updated;
use Tardigrades\SectionField\ValueObject\Version;
use Tardigrades\SectionField\ValueObject\Versioned;

/**
 * @coversDefaultClass \Tardigrades\Entity\SectionHistory
 * @covers ::__construct
 * @covers ::<private>
 */
final class SectionHistoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var Collection */
    private $fields;

    /** @var Collection */
    private $applications;

    /** @var Collection */
    private $history;

    /** @var Section */
    private $sectionHistory;

    public function setUp()
    {
        $this->fields = Mockery::mock(Collection::class);
        $this->applications = Mockery::mock(Collection::class);
        $this->history = Mockery::mock(Collection::class);
        $this->sectionHistory = new SectionHistory($this->fields, $this->applications, $this->history);
    }

    /**
     * @test
     * @covers ::setId
     */
    public function it_should_set_and_get_an_id()
    {
        $field = $this->sectionHistory->setId(5);

        $this->assertSame($this->sectionHistory, $field);
        $this->assertEquals(5, $this->sectionHistory->getId());
    }

    /**
     * @test
     * @covers ::getIdValueObject
     */
    public function it_should_get_an_id_value_object()
    {
        $this->sectionHistory->setId(10);

        $this->assertEquals(Id::fromInt(10), $this->sectionHistory->getIdValueObject());
    }

    /**
     * @test
     * @covers ::getId
     */
    public function it_should_get_a_null_asking_for_unset_id()
    {
        $this->assertEquals(null, $this->sectionHistory->getId());
    }

    /**
     * @test
     * @covers ::setName
     * @covers ::getName
     */
    public function it_should_set_and_get_name()
    {
        $name = Name::fromString('I have a name');
        $section = $this->sectionHistory->setName((string) $name);

        $this->assertSame($this->sectionHistory, $section);
        $this->assertEquals($this->sectionHistory->getName(), $name);
    }

    /**
     * @test
     * @covers ::setHandle
     * @covers ::getHandle
     */
    public function it_should_set_and_get_handle()
    {
        $handle = Handle::fromString('someHandleINeed');
        $section = $this->sectionHistory->setHandle((string) $handle);

        $this->assertSame($this->sectionHistory, $section);
        $this->assertEquals($this->sectionHistory->getHandle(), $handle);
    }

    /**
     * @test
     * @covers ::addField
     */
    public function it_should_add_a_field()
    {
        $field = Mockery::mock(FieldInterface::class);

        $field->shouldReceive('addSection')->once()->with($this->sectionHistory);
        $this->fields->shouldReceive('contains')->once()->with($field)->andReturn(false);
        $this->fields->shouldReceive('add')->once()->with($field);

        $section = $this->sectionHistory->addField($field);

        $this->assertSame($this->sectionHistory, $section);
    }

    /**
     * @test
     * @covers ::addField
     */
    public function it_should_not_add_an_existing_field()
    {
        $field = Mockery::mock(FieldInterface::class);

        $field->shouldReceive('addSection')->once()->with($this->sectionHistory);
        $this->fields->shouldReceive('contains')->once()->with($field)->andReturn(false);
        $this->fields->shouldReceive('contains')->once()->with($field)->andReturn(true);
        $this->fields->shouldReceive('add')->once()->with($field);

        $this->sectionHistory->addField($field);
        $this->sectionHistory->addField($field);
    }

    /**
     * @test
     * @covers ::removeField
     */
    public function it_should_remove_a_field()
    {
        $field = new Field();

        $this->fields
            ->shouldReceive('contains')
            ->once()
            ->with($field)
            ->andReturn(true);

        $this->fields->shouldReceive('remove')->once()->with($field);

        $fieldType = $this->sectionHistory->removeField($field);

        $this->assertEquals($this->sectionHistory, $fieldType);
    }

    /**
     * @test
     * @covers ::removeField
     */
    public function it_should_do_nothing_when_removing_non_existing_field()
    {
        $field = new Field();

        $this->fields
            ->shouldReceive('contains')
            ->once()
            ->with($field)
            ->andReturn(false);

        $this->fields->shouldReceive('remove')->never();

        $this->sectionHistory->removeField($field);
    }

    /**
     * @test
     * @covers ::getFields
     */
    public function it_should_get_fields()
    {
        $fieldOne = new Field();
        $fieldTwo = new Field();

        $sectionHistory = new SectionHistory(new ArrayCollection());

        $sectionHistory->addField($fieldOne);
        $sectionHistory->addField($fieldTwo);

        $fields = $sectionHistory->getFields();

        $this->assertSame($fields->get(0), $fieldOne);
        $this->assertSame($fields->get(1), $fieldTwo);
    }

    /**
     * @test
     * @covers ::removeFields
     */
    public function it_should_remove_all_fields()
    {
        $field = new Field();

        $this->fields->shouldReceive('contains')->once()->with($field)->andReturn(true);
        $this->fields->shouldReceive('getIterator')->once()->andReturn(new \ArrayIterator([$field]));
        $this->fields->shouldReceive('clear')->once();

        $this->sectionHistory->addField($field);
        $this->sectionHistory->removeFields();
    }

    /**
     * @test
     * @covers ::setConfig
     */
    public function it_should_set_the_section_config()
    {
        $config = [
            'section' => [
                'name' => 'I have a field name',
                'handle' => 'handle',
                'fields' => ['these', 'are', 'fields'],
                'slug' => ['these'],
                'default' => 'these',
                'namespace' => 'My\Namespace'
            ]
        ];

        $section = $this->sectionHistory->setConfig($config);

        $this->assertSame($this->sectionHistory, $section);
    }

    /**
     * @test
     * @covers ::getConfig
     */
    public function it_should_get_the_section_config()
    {
        $config = [
            'section' => [
                'name' => 'I have a field name',
                'handle' => 'handle',
                'fields' => ['these', 'are', 'fields'],
                'slug' => ['these'],
                'default' => 'these',
                'namespace' => 'My\Namespace'
            ]
        ];

        $section = $this->sectionHistory->setConfig($config);

        $this->assertEquals($this->sectionHistory->getConfig(), SectionConfig::fromArray($config));
        $this->assertSame($this->sectionHistory, $section);
    }

    /**
     * @test
     * @covers ::addApplication
     */
    public function it_should_add_an_application()
    {
        $application = Mockery::mock(Application::class);

        $application->shouldReceive('addSection')->once()->with($this->sectionHistory);
        $this->applications->shouldReceive('contains')->once()->with($application)->andReturn(false);
        $this->applications->shouldReceive('add')->once()->with($application);

        $this->sectionHistory->addApplication($application);
    }

    /**
     * @test
     * @covers ::addApplication
     */
    public function it_should_not_add_an_existing_application()
    {
        $application = Mockery::mock(Application::class);

        $application->shouldReceive('addSection')->once()->with($this->sectionHistory);
        $this->applications->shouldReceive('contains')->once()->with($application)->andReturn(false);
        $this->applications->shouldReceive('contains')->once()->with($application)->andReturn(true);
        $this->applications->shouldReceive('add')->once()->with($application);

        $this->sectionHistory->addApplication($application);
        $this->sectionHistory->addApplication($application);
    }

    /**
     * @test
     * @covers ::getApplications
     */
    public function it_should_get_all_applications()
    {
        $application = Mockery::mock(Application::class);

        $application->shouldReceive('addSection')->once()->with($this->sectionHistory);
        $this->applications->shouldReceive('contains')->once()->with($application)->andReturn(false);
        $this->applications->shouldReceive('add')->once()->with($application);

        $this->sectionHistory->addApplication($application);
        $result = $this->sectionHistory->getApplications();

        $this->assertSame($this->applications, $result);
    }

    /**
     * @test
     * @covers ::removeApplication
     */
    public function it_should_remove_an_applications()
    {
        $application = Mockery::mock(Application::class);

        $application->shouldReceive('addSection')->once()->with($this->sectionHistory);
        $this->applications->shouldReceive('contains')->once()->with($application)->andReturn(false);
        $this->applications->shouldReceive('contains')->once()->with($application)->andReturn(true);
        $this->applications->shouldReceive('add')->once()->with($application);
        $this->applications->shouldReceive('remove')->once()->with($application);

        $this->sectionHistory->addApplication($application);
        $this->sectionHistory->removeApplication($application);
    }

    /**
     * @test
     * @covers ::removeApplication
     */
    public function it_should_do_nothing_when_removing_non_existing_applications()
    {
        $application = Mockery::mock(Application::class);

        $this->applications->shouldReceive('contains')->once()->with($application)->andReturn(false);
        $this->applications->shouldReceive('remove')->never();

        $this->sectionHistory->removeApplication($application);
    }

    /**
     * @test
     * @covers ::setVersion
     * @covers ::getVersion
     */
    public function it_should_set_and_get_the_version()
    {
        $this->sectionHistory->setVersion(1);

        $this->assertEquals(Version::fromInt(1), $this->sectionHistory->getVersion());
    }

    /**
     * @test
     * @covers ::setCreated
     */
    public function it_should_set_created_date_time()
    {
        $created = new \DateTime('2017-07-02');

        $section = $this->sectionHistory->setCreated($created);

        $this->assertSame($this->sectionHistory, $section);
    }

    /**
     * @test
     * @covers ::getCreated
     */
    public function it_should_get_created_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');

        $this->sectionHistory->setCreated($dateTime);

        $this->assertEquals($this->sectionHistory->getCreated(), $dateTime);
    }

    /**
     * @test
     * @covers ::setUpdated
     */
    public function it_should_set_updated_date_time()
    {
        $updated = new \DateTime('2017-07-02');

        $section = $this->sectionHistory->setUpdated($updated);

        $this->assertSame($this->sectionHistory, $section);
    }

    /**
     * @test
     * @covers ::getUpdated
     */
    public function it_should_get_updated_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');

        $this->sectionHistory->setUpdated($dateTime);

        $this->assertEquals($this->sectionHistory->getUpdated(), $dateTime);
    }

    /**
     * @test
     * @covers ::getCreatedValueObject
     */
    public function it_should_get_a_created_value_object()
    {
        $this->sectionHistory->setCreated(new \DateTime());

        $this->assertInstanceOf(Created::class, $this->sectionHistory->getCreatedValueObject());
        $this->assertEquals(
            $this->sectionHistory->getCreatedValueObject()->getDateTime()->format('Y-m-d H:i'),
            (new \DateTime())->format('Y-m-d H:i')
        );
    }

    /**
     * @test
     * @covers ::getUpdatedValueObject
     */
    public function it_should_get_a_updated_value_object()
    {
        $this->sectionHistory->setUpdated(new \DateTime());

        $this->assertInstanceOf(Updated::class, $this->sectionHistory->getUpdatedValueObject());
        $this->assertEquals(
            $this->sectionHistory->getUpdatedValueObject()->getDateTime()->format('Y-m-d H:i'),
            (new \DateTime())->format('Y-m-d H:i')
        );
    }

    /**
     * @test
     * @covers ::setVersioned
     * @covers ::getVersioned
     */
    public function it_should_get_and_set_the_time_when_versioned()
    {
        $time = new \DateTime();
        $this->sectionHistory->setVersioned($time);

        $this->assertInstanceOf(Versioned::class, $this->sectionHistory->getVersioned());
        $this->assertEquals($time, $this->sectionHistory->getVersioned()->getDateTime());
    }

    /**
     * @test
     * @covers ::setSection
     * @covers ::getSection
     */
    public function it_should_get_and_set_a_section()
    {
        $section = new Section();
        $this->sectionHistory->setSection($section);

        $this->assertEquals($section, $this->sectionHistory->getSection());
    }

    /**
     * @test
     * @covers ::removeSection
     */
    public function it_should_remove_a_section()
    {
        $section = new Section();
        $this->sectionHistory->setSection($section);
        $this->sectionHistory->removeSection();

        $this->assertEquals(null, $this->sectionHistory->getSection());
    }
}
