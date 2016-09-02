<?php
namespace Migrator\Command;

use OutOfBoundsException;
use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class MigrateCommand extends BaseCommand
{
    const HIGHEST = 'highest';

    protected function configure()
    {
        $this
            ->setName('migrate')
            ->setDescription('Migrates the database to the given version')
            ->addArgument(
                'database',
                InputArgument::OPTIONAL,
                'Database name',
                'default'
            )
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Target version',
                self::HIGHEST
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $database = $input->getArgument('database');
        $migrator = $this->getMigrator($database);
        list($lowest, $current, $highest) = $migrator->getVersionRange();
        $target = $input->getArgument('target');
        if ($target == self::HIGHEST) {
            $target = $highest;
        }
        if ($target < $lowest || $target > $highest) {
            throw new OutOfBoundsException("Target range is $lowest:$highest");
        }
        if ($target < $current) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion(
                'Downgrade requested! Retype the target version to confirm: ',
                false,
                "/^$target$/"
            );
            if (!$helper->ask($input, $output, $question)) {
                throw new RuntimeException('Incorrect answer provided. Aborting.');
            }
        }
        $migrator->migrateTo($target);
        list($lowest, $current, $highest) = $migrator->getVersionRange();
        $this->printStatus($output, $database, $lowest, $current, $highest);
    }
}
