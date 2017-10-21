<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tardigrades\Entity\Field;
use Tardigrades\Entity\FieldType;
use Tardigrades\Entity\Section;
use PHPUnit\Framework\TestCase;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\SectionConfig;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\DoctrineSectionManager
 * @covers ::<private>
 * @covers ::__construct
 */
final class SectionManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var DoctrineSectionManager */
    private $sectionManager;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var DoctrineFieldManager */
    private $fieldManager;

    /** @var SectionHistoryManagerInterface */
    private $sectionHistoryManager;

    public function setUp()
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->fieldManager = Mockery::mock(FieldManagerInterface::class);
        $this->sectionHistoryManager = Mockery::mock(SectionHistoryManagerInterface::class);

        $this->sectionManager = new DoctrineSectionManager(
            $this->entityManager,
            $this->fieldManager,
            $this->sectionHistoryManager
        );
    }

    /**
     * @test
     * @covers ::create
     */
    public function it_should_create()
    {
        $entity = new Section();
        $this->entityManager
            ->shouldReceive('persist')
            ->once()
            ->with($entity)
            ->andReturn($entity);

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $receive = $this->sectionManager->create($entity);

        $this->assertSame($receive, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_return_a_section()
    {
        $entity = new Section();
        $id = Id::fromInt(1);
        $fieldRepository = Mockery::mock(ObjectRepository::class);
        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Section::class)
            ->andReturn($fieldRepository);
        $fieldRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn($entity);

        $section = $this->sectionManager->read($id);

        $this->assertEquals($section, $entity);
    }

    /**
     * @test
     * @covers ::read
     */
    public function it_should_read_and_throw_an_exception()
    {
        $id = Id::fromInt(20);
        $sectionRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Section::class)
            ->andReturn($sectionRepository);

        $sectionRepository
            ->shouldReceive('find')
            ->once()
            ->with($id->toInt())
            ->andReturn(null);

        $this->expectException(SectionNotFoundException::class);

        $this->sectionManager->read($id);
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_sections()
    {
        $sectionOne = new Section();
        $sectionTwo = new Section();

        $sectionRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Section::class)
            ->andReturn($sectionRepository);

        $sectionRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([$sectionOne, $sectionTwo]);

        $this->assertEquals($this->sectionManager->readAll(), [$sectionOne, $sectionTwo]);
    }

    /**
     * @test
     * @covers ::readAll
     */
    public function it_should_read_all_field_types_and_throw_an_exception()
    {
        $sectionRepository = Mockery::mock(ObjectRepository::class);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Section::class)
            ->andReturn($sectionRepository);

        $sectionRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn(null);

        $this->expectException(SectionNotFoundException::class);

        $this->sectionManager->readAll();
    }

    /**
     * @test
     * @covers ::update
     */
    public function it_should_update_a_section()
    {
        $section = new Section();

        $this->entityManager->shouldReceive('persist')->once()->with($section);
        $this->entityManager->shouldReceive('flush')->once();

        $this->sectionManager->update($section);
    }

    /**
     * @test
     * @covers ::delete
     */
    public function it_should_delete_a_section()
    {
        $section = new Section();
        $this->entityManager->shouldReceive('remove')->once()->with($section);
        $this->entityManager->shouldReceive('flush')->once();

        $this->sectionManager->delete($section);
    }

    /**
     * @test
     * @covers ::createByConfig
     */
    public function it_should_create_by_config()
    {
        $sectionConfig = SectionConfig::fromArray([
            'section' => [
                'name' => 'Super Section',
                'handle' => 'superSection',
                'fields' => [
                    'title',
                    'body',
                    'created'
                ],
                'slug' => ['title'],
                'default' => 'title',
                'namespace' => 'My\\Namespace'
            ]
        ]);

        $this->fieldManager
            ->shouldReceive('readByHandles')
            ->once();

        $this->entityManager
            ->shouldReceive('persist')
            ->once();

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $createdSection = $this->sectionManager->createByConfig($sectionConfig);

        $this->assertSame('Super Section', (string) $createdSection->getName());
        $this->assertSame('superSection', (string) $createdSection->getHandle());
        $this->assertEquals($sectionConfig, $createdSection->getConfig());
    }

    /**
     * @test
     * @covers ::updateByConfig
     * @covers ::getHighestVersion
     */
    public function it_should_update_by_config()
    {
        $sectionConfig = SectionConfig::fromArray([
            'section' => [
                'name' => 'Super Section',
                'handle' => 'superSection',
                'fields' => [
                    'title',
                    'body',
                    'created'
                ],
                'slug' => ['title'],
                'default' => 'title',
                'namespace' => 'My\Namespace'
            ]
        ]);

        $section = $this->givenASectionWithName('One');

        $this->assertSame('Section One', (string) $section->getName());
        $this->assertSame('sectionOne', (string) $section->getHandle());

        $fields = [new Field()];

        $this->fieldManager
            ->shouldReceive('readByHandles')
            ->once()
            ->andReturn($fields);

        $query = $this->givenAQueryWithResult([0 => [0, 1]]);

        $this->entityManager
            ->shouldReceive('createQuery')
            ->once()
            ->andReturn($query);

        $this->entityManager
            ->shouldReceive('persist')
            ->once();

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $this->sectionHistoryManager
            ->shouldReceive('create')
            ->once();

        $createdSection = $this->sectionManager->updateByConfig($sectionConfig, $section, true);

        $this->assertSame($section, $createdSection);
        $this->assertSame('Super Section', (string) $createdSection->getName());
        $this->assertSame('superSection', (string) $createdSection->getHandle());
        $this->assertSame(2, $section->getVersion()->toInt());
        $this->assertEquals(new ArrayCollection($fields), $section->getFields());
    }


    /**
     * @test
     * @covers ::restoreFromHistory
     * @covers ::getHighestVersion
     * @covers ::readByHandle
     * @covers ::readByHandles
     */
    public function it_should_restore_from_history()
    {
        $fields = [new Field()];

        $oldSection = $this->givenASectionWithName('Old');
        $oldSection->addField($fields[0]);

        $activeSection = $this->givenASectionWithName('Active');
        $activeSection->addField($fields[0]);

        $query = $this->givenAQueryWithResult(null);

        $fieldRepository = Mockery::mock(ObjectRepository::class);
        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Section::class)
            ->andReturn($fieldRepository);

        $this->entityManager
            ->shouldReceive('createQuery')
            ->once()
            ->andReturn($query);

        $fieldRepository
            ->shouldReceive('findBy')
            ->once()
            ->andReturn([$activeSection]);

        $this->fieldManager
            ->shouldReceive('readByHandles')
            ->once()
            ->andReturn($fields);

        $this->entityManager
            ->shouldReceive('persist')
            ->twice();

        $this->entityManager
            ->shouldReceive('flush')
            ->twice();

        $this->sectionHistoryManager
            ->shouldReceive('create')
            ->once();

        $restoredSection = $this->sectionManager->restoreFromHistory($oldSection);

        $this->assertSame($activeSection, $restoredSection);
        //different object because contents of active session is overwritten by old session
        $this->assertNotSame($oldSection, $restoredSection);
        $this->assertEquals($oldSection, $restoredSection);
        $this->assertSame('Section Old', (string) $restoredSection->getName());
        $this->assertEquals(new ArrayCollection($fields), $restoredSection->getFields());
    }

    /**
     * @test
     * @covers ::readByHandle
     */
    public function it_should_throw_exception_when_section_not_found_when_reading_by_handle()
    {
        $this->expectException(SectionNotFoundException::class);

        $handle = Handle::fromString('handleOne');

        $sectionRepository = Mockery::mock(ObjectRepository::class);
        $sectionRepository
            ->shouldReceive('findBy')
            ->once()
            ->andReturn(null);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Section::class)
            ->andReturn($sectionRepository);

        $this->sectionManager->readByHandle($handle);
    }

    /**
     * @test
     * @covers ::readByHandles
     */
    public function it_should_read_by_handles()
    {
        $handle1 = Handle::fromString('handleOne');
        $handle2 = Handle::fromString('handleTwo');
        $handles = [$handle1, $handle2];

        $section = $this->givenASectionWithName('Name');
        $query = $this->givenAQueryWithResult([$section]);

        $this->entityManager
            ->shouldReceive('createQuery')
            ->once()
            ->andReturn($query);

        $result = $this->sectionManager->readByHandles($handles);
        $this->assertSame($section, $result[0]);
    }

    /**
     * @test
     * @covers ::readByHandles
     */
    public function it_should_throw_exception_when_section_not_found_when_reading_by_handles()
    {
        $this->expectException(SectionNotFoundException::class);
        $handle1 = Handle::fromString('handleOne');
        $handle2 = Handle::fromString('handleTwo');
        $handles = [$handle1, $handle2];

        $query = $this->givenAQueryWithResult(null);

        $this->entityManager
            ->shouldReceive('createQuery')
            ->once()
            ->andReturn($query);

        $this->sectionManager->readByHandles($handles);
    }

    /**
     * @test
     * @covers ::getRelationshipsOfAll
     */
    public function it_should_get_relationships_of_all()
    {
        $sectionOne = $this->givenASectionWithName('One');
        $sectionTwo = $this->givenASectionWithName('Two');

        $fieldOne = $this->givenAFieldWithNameKindAndTo('One', 'many-to-one', 'Two');
        $fieldTwo = $this->givenAFieldWithNameKindAndTo('Two', 'one-to-many', 'One');

        $sectionRepository = Mockery::mock(ObjectRepository::class);
        $sectionRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn([$sectionOne, $sectionTwo]);

        $this->entityManager
            ->shouldReceive('getRepository')
            ->once()
            ->with(Section::class)
            ->andReturn($sectionRepository);

        $this->fieldManager
            ->shouldReceive('readByHandles')
            ->once()
            ->with(['title', 'body', 'created'])
            ->andReturn([$fieldOne]);

        $this->fieldManager
            ->shouldReceive('readByHandles')
            ->once()
            ->with(['title', 'body', 'created'])
            ->andReturn([$fieldTwo]);

        $result = $this->sectionManager->getRelationshipsOfAll();

        $expected = [
            'sectionOne' => [
                'fieldOne' => [
                    'kind' => 'many-to-one',
                    'to' => 'sectionTwo',
                    'from' => 'sectionOne',
                    'fullyQualifiedClassName' => FullyQualifiedClassName::fromString(
                        '\\My\\Namespace\\FieldTypeClassOne'
                    ),
                    'relationship-type' => 'unidirectional'
                ],
                'fieldTwo-opposite' => [
                    'kind' => 'many-to-one',
                    'to' => 'sectionTwo',
                    'fullyQualifiedClassName' => FullyQualifiedClassName::fromString(
                        '\\My\\Namespace\\FieldTypeClassTwo'
                    ),
                    'relationship-type' => 'bidirectional'
                ]
            ],
            'sectionTwo' => [
                'fieldTwo' => [
                    'kind' => 'one-to-many',
                    'to' => 'sectionOne',
                    'from' => 'sectionTwo',
                    'fullyQualifiedClassName' => FullyQualifiedClassName::fromString(
                        '\\My\\Namespace\\FieldTypeClassTwo'
                    ),
                    'relationship-type' => 'unidirectional'
                ],
                'fieldOne-opposite' => [
                    'kind' => 'one-to-many',
                    'to' => 'sectionOne',
                    'fullyQualifiedClassName' => FullyQualifiedClassName::fromString(
                        '\\My\\Namespace\\FieldTypeClassOne'
                    ),
                    'relationship-type' => 'bidirectional'
                ]
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    private function givenASectionWithName($name)
    {
        $sectionName = 'Section ' . $name;
        $sectionHandle = 'section' . $name;

        $sectionConfig = SectionConfig::fromArray([
            'section' => [
                'name' => $sectionName,
                'handle' => $sectionHandle,
                'fields' => [
                    'title',
                    'body',
                    'created'
                ],
                'slug' => ['title'],
                'default' => 'title',
                'namespace' => 'My\\Namespace'
            ]
        ]);

        $section = new Section();

        $section->setName($sectionName);
        $section->setHandle($sectionHandle);
        $section->setConfig($sectionConfig->toArray());
        $section->setVersion(1);
        $section->setCreated(new \DateTime());
        $section->setUpdated(new \DateTime());

        return $section;
    }

    private function givenAQueryWithResult($result)
    {
        $query = Mockery::mock(AbstractQuery::class);
        $query->shouldReceive('getResult')
            ->once()
            ->andReturn($result);

        return $query;
    }

    private function givenAFieldWithNameKindAndTo($name, $kind, $to)
    {
        $fieldName = 'Field ' . $name;
        $fieldHandle = 'field' . $name;
        $field = new Field();
        $field->setName($fieldName);
        $field->setHandle($fieldHandle);

        $fieldConfig = FieldConfig::fromArray([
            'field' => [
                'name' => $fieldName,
                'handle' => $fieldHandle,
                'kind' => $kind,
                'to' => 'section' . $to,
                'relationship-type' => 'unidirectional'
            ]
        ]);

        $field->setConfig($fieldConfig->toArray());
        $fieldType = new FieldType();
        $fieldType->setFullyQualifiedClassName('\\My\\Namespace\\FieldTypeClass' . $name);
        $field->setFieldType($fieldType);

        return $field;
    }
}
