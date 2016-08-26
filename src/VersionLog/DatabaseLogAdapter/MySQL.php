<?php
namespace Migrator\VersionLog\DatabaseLogAdapter;

use PDO;

class MySQL extends AbstractAdapter
{
    /**
     * Initialize version log
     * @param PDO $pdo
     * @return void
     */
    public function init(PDO $pdo)
    {
        $pdo->exec("
          CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            version INT NOT NULL,
            ts TIMESTAMP NOT NULL DEFAULT now()
          )
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
        return (int) $select->fetchColumn(); // returns 0 if the result set is empty
    }

    /**
     * Set version to the new value
     * @param PDO $pdo
     * @param int $new_version
     */
    public function updateVersion(PDO $pdo, $new_version)
    {
        $insert = $pdo->prepare("INSERT INTO {$this->table} (version) VALUES (:ver)");
        $insert->execute([
            ':ver' => $new_version,
        ]);
    }
}
