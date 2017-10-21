<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Mockery;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tardigrades\Entity\Application;
use Tardigrades\Entity\Language;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\ValueObject\ApplicationConfig;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\Service\LanguageManagerInterface;

/**
 * @coversDefaultClass Tardigrades\SectionField\Service\CreateSection
 * @covers ::<private>
 * @covers ::__construct
 */
final class CreateSectionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var CreateSectionInterface|Mockery\MockInterface[] */
    private $creators;

    /** @var EventDispatcherInterface|Mockery\MockInterface */
    private $dispatcher;

    /** @var CreateSection */
    private $createSection;

    public function setUp()
    {
        $this->creators = [Mockery::mock(CreateSectionInterface::class)];
        $this->dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $this->createSection = new CreateSection(
            $this->creators,
            $this->dispatcher
        );
    }

    /**
     * @test
     * @covers ::save
     */
    public function it_should_save()
    {
        $entry = Mockery::mock(CommonSectionInterface::class);
        $entry->shouldReceive('getId')->once();
        $this->creators[0]->shouldReceive('save')->once();

        $this->dispatcher->shouldReceive('dispatch')->twice();
        $this->createSection->save($entry);
    }
}
