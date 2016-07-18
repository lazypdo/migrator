<?php
namespace Migrator\Factory;

use InvalidArgumentException;
use Migrator\Factory\Config\ProviderInterface;
use Migrator\MigrationReader\SingleFolderCallbackMigrationReader;
use Migrator\Migrator;
use Migrator\VersionLog\DatabaseLog;
use OutOfBoundsException;
use PDO;

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
                'options'  => [],
                'user'     => null,
                'password' => null,
            ],
            $this->provider->getConfig($name)
        );
        try {
            $dsn = $this->getKey('dsn', $config);
            $pdo = new PDO(
                $dsn,
                $this->getKey('user', $config),
                $this->getKey('password', $config),
                $this->getKey('options', $config)
            );
            $reader = new SingleFolderCallbackMigrationReader(
                $this->getKey('migrations', $config)
            );
        } catch (OutOfBoundsException $e) {
            throw new InvalidArgumentException(
                "Value for '{$e->getMessage()}' must be configured for database '$name'"
            );
        }
        $log = new DatabaseLog();
        return new Migrator($pdo, $reader, $log);
    }

    /**
     * @param string $key
     * @param array  $conf
     * @return mixed
     */
    private function getKey($key, array $conf)
    {
        if (array_key_exists($key, $conf)) {
            return $conf[$key];
        }
        throw new OutOfBoundsException($key);
    }
}
