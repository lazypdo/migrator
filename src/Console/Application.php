<?php
namespace Migrator\Console;

use Migrator\Command\MigrateCommand;
use Migrator\Command\StatusCommand;
use Migrator\Factory\Config\JSONConfigProvider;
use Migrator\Factory\Config\PHPConfigProvider;
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

    const DEFAULT_CONFIG_FILE = 'migrator.php';

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
        if ($this->getFactory() instanceof FactoryInterface) {
            $this->setDefaultFactory($input);
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
     * @param FactoryInterface $factory
     */
    public function setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;
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

    private function setDefaultFactory(InputInterface $input)
    {
        $config_file = $input->getParameterOption(['--config', '-c'], self::DEFAULT_CONFIG_FILE);
        $ext = pathinfo($config_file, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'php':
                $provider = new PHPConfigProvider($config_file);
                break;
            default:
                $provider = new JSONConfigProvider($config_file);
        }
        $this->setFactory(new ConfigurableFactory($provider));
    }
}
