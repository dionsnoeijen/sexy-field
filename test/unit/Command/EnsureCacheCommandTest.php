<?php
declare(strict_types=1);

namespace Tardigrades\Command;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\PdoAdapter;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass Tardigrades\Command\EnsureCacheCommand
 * @covers ::__construct
 * @covers ::<protected>
 * @covers ::<private>
 */
final class EnsureCacheCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var Mock|PdoAdapter */
    private $adapter;

    /** @var EnsureCacheCommand */
    private $command;

    public function setUp()
    {
        $this->adapter = \Mockery::mock(PdoAdapter::class);
        $this->command = new EnsureCacheCommand($this->adapter);
    }

    /** @test */
    public function it_works_if_table_does_not_exist_yet()
    {
        $this->adapter->shouldReceive('createTable');
        $this->checkForOutput("Caching table created!\n");
    }

    /** @test */
    public function it_works_if_table_already_exists()
    {
        $this->adapter->shouldReceive('createTable')->andThrow(\PDOException::class);
        $this->checkForOutput("Got database error, assuming caching table already exists\n");
    }

    /** @test */
    public function it_can_be_verbose()
    {
        $this->adapter->shouldReceive('createTable')->andThrow(\PDOException::class, 'Uh oh');
        $this->checkForOutput(
            "Uh oh\nGot database error, assuming caching table already exists\n",
            ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]
        );
    }

    private function checkForOutput(string $output, array $options = [], array $input = [])
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute($input, $options);
        $this->assertSame($output, $commandTester->getDisplay());
    }
}
