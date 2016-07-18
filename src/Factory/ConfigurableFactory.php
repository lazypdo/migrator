<?php
namespace Migrator\Factory;

use Migrator\Factory\Config\ProviderInterface;
use Migrator\MigrationReader\SingleFolder;
use Migrator\Migrator;
use Migrator\VersionLog\DatabaseLog;
use PDO;
use RuntimeException;

class ConfigurableFactory implements FactoryInterface
{
    /**
     * @var ProviderInterface
     */
    private $provider;

    /**
     * ConfigurableFactory constructor.
     * @param ProviderInterface $provider
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @inheritdoc
     */
    public function getMigrator($database)
    {
        $config = array_merge(
            [
                'options'    => [],
                'user'       => null,
                'password'   => null,
                'migrations' => 'migrations',
            ],
            $this->provider->getConfig($database)
        );
        if (empty($config['dsn'])) {
            throw new RuntimeException("DSN in not configured for database $database");
        }
        $pdo = new PDO(
            $config['dsn'],
            $config['user'],
            $config['password'],
            $config['options']
        );
        $reader = new SingleFolder($config['migrations']);
        $log = new DatabaseLog();
        return new Migrator($pdo, $reader, $log);
    }
}
