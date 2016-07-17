<?php
namespace Migrator;

use OutOfRangeException;
use PDO;
use RuntimeException;
use Throwable;

class Migrator
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var VersionLogInterface
     */
    protected $version_log;

    /**
     * @var MigrationReaderInterface
     */
    protected $migration_reader;

    /**
     * Migration constructor.
     * @param PDO                      $pdo
     * @param MigrationReaderInterface $migration_reader
     * @param VersionLogInterface      $version_log
     */
    public function __construct(PDO $pdo, MigrationReaderInterface $migration_reader, VersionLogInterface $version_log)
    {
        $this->pdo = $pdo;
        $this->version_log = $version_log;
        $this->migration_reader = $migration_reader;
    }

    /**
     * Get the possible version range. The lowest, current, and highest version
     * @return int[] [$lowest, $current, $highest]
     */
    public function getVersionRange(): array
    {
        $current = $this->version_log->getCurrentVersion($this->pdo);
        for ($highest = $current; $this->migration_reader->upgradeExistsTo($highest + 1); $highest++);
        for ($lowest = $current + 1; $this->migration_reader->downgradeExistsFrom($lowest - 1); $lowest--);
        return [$lowest - 1, $current, $highest];
    }

    /**
     * Migrate to version
     * @param int $to_version
     * @throws Throwable
     */
    public function migrateTo(int $to_version)
    {
        list($lowest, $current, $highest) = $this->getVersionRange();
        if ($to_version < $lowest || $to_version > $highest) {
            throw new OutOfRangeException('Target version out of range');
        }
        if ($to_version == $current) {
            return;
        }
        $this->pdo->beginTransaction();

        try {
            if ($to_version > $current) {
                $this->applyUpgradesTo(range($current + 1, $to_version, 1));
            } else {
                $this->applyDowngradesFrom(range($current, $to_version + 1, -1));
            }
            $this->version_log->updateVersion($this->pdo, $to_version);
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Apply upgrades to the given versions
     * @param int[] $versions
     */
    protected function applyUpgradesTo(array $versions)
    {
        foreach ($versions as $version) {
            $migration = $this->migration_reader->getUpgradeTo($version);
            $this->exec($migration);
        }
    }

    /**
     * Apply downgrades from the given versions
     * @param int[] $versions
     */
    protected function applyDowngradesFrom(array $versions)
    {
        foreach ($versions as $version) {
            $migration = $this->migration_reader->getDowngradeFrom($version);
            $this->exec($migration);
        }
    }

    /**
     * @param string $sql
     */
    protected function exec(string $sql)
    {
        $result = $this->pdo->exec($sql);
        if ($result === false) {
            throw new RuntimeException("Query failed:\n{$sql}");
        }
    }
}
