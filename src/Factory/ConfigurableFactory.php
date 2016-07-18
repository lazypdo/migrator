<?php
namespace Migrator\Factory;

use Migrator\Factory\Config\ProviderInterface;
use Migrator\MigrationReader\SingleFolderCallbackMigrationReader;
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
     * Get an instance of Migrator for the given config
     * @param string $name
     * @return Migrator
     */
    public function getMigrator($name)
    {
        $config = array_merge(
            [
                'options'    => [],
                'user'       => null,
                'password'   => null,
                'migrations' => 'migrations',
            ],
            $this->provider->getConfig($name)
        );
        if (empty($config['dsn'])) {
            throw new RuntimeException("DSN in not configured for database $name");
        }
        $pdo = new PDO(
            $config['dsn'],
            $config['user'],
            $config['password'],
            $config['options']
        );
        $reader = new SingleFolderCallbackMigrationReader($config['migrations']);
        $log = new DatabaseLog();
        return new Migrator($pdo, $reader, $log);
    }
}
