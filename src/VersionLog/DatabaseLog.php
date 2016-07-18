<?php
namespace Migrator\VersionLog;

use Migrator\VersionLog\DatabaseLogAdapter\Factory;
use Migrator\VersionLog\DatabaseLogAdapter\FactoryInterface;
use Migrator\VersionLogInterface;
use PDO;

class DatabaseLog implements VersionLogInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * DatabaseLog constructor.
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory = null)
    {
        $this->factory = $factory ?: new Factory();
    }

    /**
     * Get current version
     * @param PDO $pdo
     * @return int
     */
    public function getCurrentVersion(PDO $pdo)
    {
        $adapter = $this->factory->getAdapter($pdo);
        $adapter->init($pdo);
        return $adapter->getCurrentVersion($pdo);
    }

    /**
     * Set version to the new value
     * @param PDO $pdo
     * @param int $new_version
     * @return void
     * @internal param int $version
     */
    public function updateVersion(PDO $pdo, $new_version)
    {
        $adapter = $this->factory->getAdapter($pdo);
        $adapter->init($pdo);
        $adapter->updateVersion($pdo, $new_version);
    }
}
