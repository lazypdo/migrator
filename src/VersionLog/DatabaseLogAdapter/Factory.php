<?php
namespace Migrator\VersionLog\DatabaseLogAdapter;

use PDO;
use RuntimeException;

class Factory implements FactoryInterface
{
    /**
     * @var AbstractAdapter[]
     */
    private $adapters = [];

    /**
     * @param PDO $pdo
     * @return AbstractAdapter
     */
    public function getAdapter(PDO $pdo)
    {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if (isset($this->adapters[$driver])) {
            return $this->adapters[$driver];
        }
        switch ($driver) {
            case 'sqlite':
            case 'sqlite2':
                $adapter = new SQLite();
                break;
            case 'pgsql':
                $adapter = new PostgreSQL();
                break;
            default:
                throw new RuntimeException("Adapter for $driver is not yet implemented. Mind opening a pull request?");
        }
        return $this->adapters[$driver] = $adapter;
    }
}
