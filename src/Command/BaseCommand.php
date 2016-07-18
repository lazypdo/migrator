<?php
namespace Migrator\Command;

use LogicException;
use Migrator\Console\Application;
use Migrator\Migrator;
use Migrator\Factory\FactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends Command implements FactoryInterface
{
    /**
     * Get an instance of Migrator for the given database
     * @param string $database
     * @return Migrator
     */
    public function getMigrator($database)
    {
        $app = $this->getApplication();
        if ($app instanceof Application) {
            return $app->getFactory()->getMigrator($database);
        }
        throw new LogicException('Application is expected to be an instance of \\Migrator\\Console\\Application');
    }

    /**
     * @param OutputInterface $output
     * @param string          $name
     * @param int             $lowest
     * @param int             $current
     * @param int             $highest
     */
    protected function printStatus(OutputInterface $output, $name, $lowest, $current, $highest)
    {
        $upgrade = ($highest > $current) ? $highest : 'none';
        $downgrade = ($lowest < $current) ? $lowest : 'none';
        $output->writeln("Database:        <info>$name</info>");
        $output->writeln("At version:      <info>$current</info>");
        $output->writeln("Upgradable to:   <info>$upgrade</info>");
        $output->writeln("Downgradable to: <info>$downgrade</info>");
    }
}
