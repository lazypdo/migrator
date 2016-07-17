<?php
namespace Migrator\Console;

use Migrator\Command\MigrateCommand;
use Migrator\Command\StatusCommand;
use Migrator\Factory\FactoryInterface;
use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    const VERSION = '0.0.0';

    /**
     * @var FactoryInterface
     */
    private $factory;
    
    public function __construct(FactoryInterface $factory)
    {
        parent::__construct('Migrator', self::VERSION);
        $this->addCommands([
            new StatusCommand(),
            new MigrateCommand(),
        ]);
        $this->factory = $factory;
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }
}
