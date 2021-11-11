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

/**
 * @coversDefaultClass \Tardigrades\Entity\Section
 * @covers ::__construct
 * @covers ::<private>
 */
final class SectionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var Collection */
    private $fields;

    /** @var Collection */
    private $applications;

    /** @var Collection */
    private $history;

    /** @var Section */
    private $section;

    public function setUp(): void
    {
        $this->fields = Mockery::mock(Collection::class);
        $this->applications = Mockery::mock(Collection::class);
        $this->history = Mockery::mock(Collection::class);
        $this->section = new Section($this->fields, $this->applications, $this->history);
    }

    /**
     * @test
     * @covers ::setId
     */
    public function it_should_set_and_get_an_id()
    {
        $field = $this->section->setId(5);

        $this->assertSame($this->section, $field);
        $this->assertEquals(5, $this->section->getId());
    }

    /**
     * @test
     * @covers ::getIdValueObject
     */
    public function it_should_get_an_id_value_object()
    {
        $this->section->setId(10);

        $this->assertEquals(Id::fromInt(10), $this->section->getIdValueObject());
    }

    /**
     * @test
     * @covers ::getId
     */
    public function it_should_get_a_null_asking_for_unset_id()
    {
        $this->assertEquals(null, $this->section->getId());
    }

    /**
     * @test
     * @covers ::setName
     * @covers ::getName
     */
    public function it_should_set_and_get_name()
    {
        $name = Name::fromString('I have a name');
        $section = $this->section->setName((string) $name);

        $this->assertSame($this->section, $section);
        $this->assertEquals($this->section->getName(), $name);
    }

    /**
     * @test
     * @covers ::setHandle
     * @covers ::getHandle
     */
    public function it_should_set_and_get_handle()
    {
        $handle = Handle::fromString('someHandleINeed');
        $section = $this->section->setHandle((string) $handle);

        $this->assertSame($this->section, $section);
        $this->assertEquals($this->section->getHandle(), $handle);
    }

    /**
     * @test
     * @covers ::addField
     */
    public function it_should_add_a_field()
    {
        $field = Mockery::mock(FieldInterface::class);

        $field->shouldReceive('addSection')->once()->with($this->section);
        $this->fields->shouldReceive('contains')->once()->with($field)->andReturn(false);
        $this->fields->shouldReceive('add')->once()->with($field);

        $section = $this->section->addField($field);

        $this->assertSame($this->section, $section);
    }

    /**
     * @test
     * @covers ::addField
     */
    public function it_should_not_add_an_existing_field()
    {
        $field = Mockery::mock(FieldInterface::class);

        $field->shouldReceive('addSection')->once()->with($this->section);
        $this->fields->shouldReceive('contains')->once()->with($field)->andReturn(false);
        $this->fields->shouldReceive('contains')->once()->with($field)->andReturn(true);
        $this->fields->shouldReceive('add')->once()->with($field);

        $this->section->addField($field);
        $this->section->addField($field);
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

        $this->fields->shouldReceive('removeElement')->once()->with($field);

        $fieldType = $this->section->removeField($field);

        $this->assertEquals($this->section, $fieldType);
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

        $this->fields->shouldReceive('removeElement')->never();

        $this->section->removeField($field);
    }

    /**
     * @test
     * @covers ::getFields
     */
    public function it_should_get_fields()
    {
        $fieldOne = new Field();
        $fieldTwo = new Field();

        $section = new Section(new ArrayCollection());

        $section->addField($fieldOne);
        $section->addField($fieldTwo);

        $fields = $section->getFields();

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

        $this->section->addField($field);
        $this->section->removeFields();
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

        $section = $this->section->setConfig($config);

        $this->assertSame($this->section, $section);
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

        $section = $this->section->setConfig($config);

        $this->assertEquals($this->section->getConfig(), SectionConfig::fromArray($config));
        $this->assertSame($this->section, $section);
    }

    /**
     * @test
     * @covers ::addApplication
     */
    public function it_should_add_an_application()
    {
        $application = Mockery::mock(Application::class);

        $application->shouldReceive('addSection')->once()->with($this->section);
        $this->applications->shouldReceive('contains')->once()->with($application)->andReturn(false);
        $this->applications->shouldReceive('add')->once()->with($application);

        $this->section->addApplication($application);
    }

    /**
     * @test
     * @covers ::addApplication
     */
    public function it_should_not_add_an_existing_application()
    {
        $application = Mockery::mock(Application::class);

        $application->shouldReceive('addSection')->once()->with($this->section);
        $this->applications->shouldReceive('contains')->once()->with($application)->andReturn(false);
        $this->applications->shouldReceive('contains')->once()->with($application)->andReturn(true);
        $this->applications->shouldReceive('add')->once()->with($application);

        $this->section->addApplication($application);
        $this->section->addApplication($application);
    }

    /**
     * @test
     * @covers ::getApplications
     */
    public function it_should_get_all_applications()
    {
        $application = Mockery::mock(Application::class);

        $application->shouldReceive('addSection')->once()->with($this->section);
        $this->applications->shouldReceive('contains')->once()->with($application)->andReturn(false);
        $this->applications->shouldReceive('add')->once()->with($application);

        $this->section->addApplication($application);
        $result = $this->section->getApplications();

        $this->assertSame($this->applications, $result);
    }

    /**
     * @test
     * @covers ::removeApplication
     */
    public function it_should_remove_an_application()
    {
        $application = Mockery::mock(Application::class);

        $application->shouldReceive('addSection')->once()->with($this->section);
        $this->applications->shouldReceive('contains')->once()->with($application)->andReturn(false);
        $this->applications->shouldReceive('contains')->once()->with($application)->andReturn(true);
        $this->applications->shouldReceive('add')->once()->with($application);
        $this->applications->shouldReceive('removeElement')->once()->with($application);

        $this->section->addApplication($application);
        $this->section->removeApplication($application);
    }

    /**
     * @test
     * @covers ::removeApplication
     */
    public function it_should_do_nothing_when_removing_non_existing_applications()
    {
        $application = Mockery::mock(Application::class);

        $this->applications->shouldReceive('contains')->once()->with($application)->andReturn(false);
        $this->applications->shouldReceive('removeElement')->never();

        $this->section->removeApplication($application);
    }

    /**
     * @test
     * @covers ::setVersion
     * @covers ::getVersion
     */
    public function it_should_set_and_get_the_version()
    {
        $this->section->setVersion(1);

        $this->assertEquals(Version::fromInt(1), $this->section->getVersion());
    }

    /**
     * @test
     * @covers ::addHistory
     */
    public function it_should_add_section_to_history_only_once()
    {
        $section = new SectionHistory();

        $this->history->shouldReceive('contains')->once()->with($section)->andReturn(false);
        $this->history->shouldReceive('contains')->once()->with($section)->andReturn(true);
        $this->history->shouldReceive('add')->once()->with($section);
        $this->section->addHistory($section);
        $this->section->addHistory($section);
    }

    /**
     * @test
     * @covers ::getHistory
     */
    public function it_should_get_the_history()
    {
        $sectionHistory = new SectionHistory();
        $section = new Section();
        $section->addHistory($sectionHistory);
        $history = $section->getHistory();
        $this->assertTrue($history->contains($sectionHistory));
    }

    /**
     * @test
     * @covers ::addHistory
     * @covers ::removeHistory
     */
    public function it_should_remove_the_history()
    {
        $sectionHistory = new SectionHistory();
        $this->history->shouldReceive('contains')->once()->with($sectionHistory)->andReturn(false);
        $this->history->shouldReceive('contains')->once()->with($sectionHistory)->andReturn(true);
        $this->history->shouldReceive('add')->once()->with($sectionHistory);
        $this->history->shouldReceive('removeElement')->once()->with($sectionHistory);

        $this->section->addHistory($sectionHistory);
        $this->section->removeHistory($sectionHistory);
    }

    /**
     * @test
     * @covers ::addHistory
     * @covers ::removeHistory
     */
    public function it_should_do_nothing_when_removing_non_existing_section_from_history()
    {
        $sectionHistory = new SectionHistory();
        $this->history->shouldReceive('contains')->once()->with($sectionHistory)->andReturn(false);
        $this->history->shouldReceive('removeElement')->never();

        $this->section->removeHistory($sectionHistory);
    }

    /**
     * @test
     * @covers ::setCreated
     */
    public function it_should_set_created_date_time()
    {
        $created = new \DateTime('2017-07-02');

        $section = $this->section->setCreated($created);

        $this->assertSame($this->section, $section);
    }

    /**
     * @test
     * @covers ::getCreated
     */
    public function it_should_get_created_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');

        $this->section->setCreated($dateTime);

        $this->assertEquals($this->section->getCreated(), $dateTime);
    }

    /**
     * @test
     * @covers ::setUpdated
     */
    public function it_should_set_updated_date_time()
    {
        $updated = new \DateTime('2017-07-02');

        $section = $this->section->setUpdated($updated);

        $this->assertSame($this->section, $section);
    }

    /**
     * @test
     * @covers ::getUpdated
     */
    public function it_should_get_updated_date_time()
    {
        $dateTime = new \DateTime('2017-07-02');

        $this->section->setUpdated($dateTime);

        $this->assertEquals($this->section->getUpdated(), $dateTime);
    }

    /**
     * @test
     * @covers ::onPrePersist
     */
    public function it_should_update_dates_on_pre_persist()
    {
        $this->section->onPrePersist();

        $created = new \DateTime("now");
        $updated = new \DateTime("now");

        $this->assertEquals(
            $this->section
                ->getCreated()
                ->format('Y-m-d H:i'),
            $created
                ->format('Y-m-d H:i')
        );
        $this->assertEquals(
            $this->section
                ->getUpdated()
                ->format('Y-m-d H:i'),
            $updated
                ->format('Y-m-d H:i')
        );
    }

    /**
     * @test
     * @covers ::onPreUpdate
     */
    public function it_should_update_update_date_on_pre_update()
    {
        $this->section->onPreUpdate();

        $updated = new \DateTime("now");

        $this->assertEquals(
            $this->section
                ->getUpdated()
                ->format('Y-m-d H:i'),
            $updated
                ->format('Y-m-d H:i')
        );
    }

    /**
     * @test
     * @covers ::getCreatedValueObject
     */
    public function it_should_get_a_created_value_object()
    {
        $this->section->setCreated(new \DateTime());

        $this->assertInstanceOf(Created::class, $this->section->getCreatedValueObject());
        $this->assertEquals(
            $this->section->getCreatedValueObject()->getDateTime()->format('Y-m-d H:i'),
            (new \DateTime())->format('Y-m-d H:i')
        );
    }

    /**
     * @test
     * @covers ::getUpdatedValueObject
     */
    public function it_should_get_a_updated_value_object()
    {
        $this->section->setUpdated(new \DateTime());

        $this->assertInstanceOf(Updated::class, $this->section->getUpdatedValueObject());
        $this->assertEquals(
            $this->section->getUpdatedValueObject()->getDateTime()->format('Y-m-d H:i'),
            (new \DateTime())->format('Y-m-d H:i')
        );
    }
}
