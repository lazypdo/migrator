<?php
namespace Migrator\VersionLog\DatabaseLogAdapter;

use PDO;

class SQLite extends AbstractAdapter
{
    /**
     * Initialize version log
     * @param PDO $pdo
     * @return void
     */
    public function init(PDO $pdo)
    {
        $pdo->exec("
          CREATE TABLE IF NOT EXISTS {$this->table} 
          (id INTEGER PRIMARY KEY, version INTEGER NOT NULL, ts TEXT NOT NULL)
        ");
    }

    /**
     * Get current version
     * @param PDO $pdo
     * @return int
     */
    public function getCurrentVersion(PDO $pdo)
    {
        $select = $pdo->prepare("SELECT version FROM {$this->table} ORDER BY id DESC LIMIT 1");
        $select->execute();
        return (int) $select->fetchColumn();
    }

    /**
     * Set version to the new value
     * @param PDO $pdo
     * @param int $new_version
     * @return void
     */
    public function updateVersion(PDO $pdo, int $new_version)
    {
        $insert = $pdo->prepare("INSERT INTO {$this->table} (version, ts) VALUES (:ver, datetime('now'))");
        $insert->execute([
            ':ver' => $new_version,
        ]);    
    }

}
