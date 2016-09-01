<?php
namespace Migrator\Console;

use Migrator\Command\MigrateCommand;
use Migrator\Command\StatusCommand;
use Migrator\Factory\Config\JSONConfigProvider;
use Migrator\Factory\Config\PHPConfigProvider;
use Migrator\Factory\Config\YAMLConfigProvider;
use Migrator\Factory\ConfigurableFactory;
use Migrator\Factory\FactoryInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends ConsoleApplication
{
    const VERSION = '1.1';

    const DEFAULT_CONFIG_FILE = 'migrator.php';

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * Application constructor.
     * @param FactoryInterface|null $factory
     */
    public function __construct(FactoryInterface $factory = null)
    {
        if ($factory) {
            $this->factory = $factory;
        }
        parent::__construct('Migrator', self::VERSION);
        $this->addCommands([
            new StatusCommand(),
            new MigrateCommand(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if (!$this->factory) {
            $config_file = $input->getParameterOption(['--config', '-c'], self::DEFAULT_CONFIG_FILE);
            switch (pathinfo($config_file, PATHINFO_EXTENSION)) {
                case 'php':
                    $provider = new PHPConfigProvider($config_file);
                    break;
                case 'yaml':
                case 'yml':
                    $provider = new YAMLConfigProvider($config_file);
                    break;
                default:
                    $provider = new JSONConfigProvider($config_file);
            }
            $this->factory = new ConfigurableFactory($provider);
        }

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
     * @inheritdoc
     */
    protected function getDefaultInputDefinition()
    {
        $def = parent::getDefaultInputDefinition();

        if (!$this->factory) {
            $def->addOption(
                new InputOption(
                    '--config',
                    '-c',
                    InputOption::VALUE_OPTIONAL,
                    'Configuration file',
                    self::DEFAULT_CONFIG_FILE
                )
            );
        }
        return $def;
    }
}
