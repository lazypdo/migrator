<?php
namespace Migrator\Console;

use Migrator\Command\MigrateCommand;
use Migrator\Command\StatusCommand;
use Migrator\Factory\Config\JSONConfigProvider;
use Migrator\Factory\ConfigurableFactory;
use Migrator\Factory\FactoryInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends ConsoleApplication
{
    const VERSION = '0.0.0';

    const DEFAULT_CONFIG_FILE = 'migrator.json';

    /**
     * @var FactoryInterface
     */
    private $factory;
    
    public function __construct()
    {
        parent::__construct('Migrator', self::VERSION);
        $this->addCommands([
            new StatusCommand(),
            new MigrateCommand(),
        ]);
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $config_file = $input->getParameterOption(['--config', '-c'], self::DEFAULT_CONFIG_FILE);
        $this->factory = new ConfigurableFactory(
            new JSONConfigProvider($config_file)
        );
        return parent::doRun($input, $output);
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        $def = parent::getDefaultInputDefinition();
        $def->addOption(
            new InputOption(
                '--config',
                '-c',
                InputOption::VALUE_OPTIONAL,
                'Configuration file',
                self::DEFAULT_CONFIG_FILE
            )
        );
        return $def;
    }
}
