<?php
declare(strict_types=1);

namespace Tardigrades\Command;

use Doctrine\DBAL\DBALException;
use Symfony\Component\Cache\Adapter\PdoAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EnsureCacheCommand extends Command
{
    /** @var PdoAdapter */
    private $adapter;

    public function __construct(PdoAdapter $adapter)
    {
        parent::__construct('sf:ensure-cache');
        $this->adapter = $adapter;
    }

    protected function configure()
    {
        $this->setDescription("Create the caching table in the database, if it doesn't exist");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->adapter->createTable();
            $output->writeln("<info>Caching table created!</info>");
            return 0;
        } catch (\PDOException | \Exception $exception) {
            $output->writeln($exception->getMessage(), OutputInterface::VERBOSITY_VERBOSE);
            $output->writeln("<info>Got database error, assuming caching table already exists</info>");
            return 1;
        }
    }
}
