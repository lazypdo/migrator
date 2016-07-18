<?php
namespace Migrator\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Status
 * @package Migrator\Command
 */
class StatusCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('status')
            ->setDescription('Database status')
            ->addArgument(
                'database',
                InputArgument::OPTIONAL,
                'Database name',
                'default'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $migrator = $this->getMigrator($name);
        list($lowest, $current, $highest) = $migrator->getVersionRange();
        $this->printStatus($output, $name, $lowest, $current, $highest);
    }
}
