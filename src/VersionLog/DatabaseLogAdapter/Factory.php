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
     * @var string
     */
    private $table_name;

    /**
     * Factory constructor.
     * @param string $table_name
     */
    public function __construct($table_name = AbstractAdapter::DEFAULT_TABLE_NAME)
    {
        $this->table_name = $table_name;
    }

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
                $adapter = new SQLite($this->table_name);
                break;
            case 'pgsql':
                $adapter = new PostgreSQL($this->table_name);
                break;
            default:
                throw new RuntimeException("Adapter for $driver is not yet implemented. Mind opening a pull request?");
        }
        return $this->adapters[$driver] = $adapter;
    }
}
