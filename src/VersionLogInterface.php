<?php

namespace Migrator;

use PDO;

interface VersionLogInterface
{
    /**
     * Get current version
     * @param PDO $pdo
     * @return int
     */
    public function getCurrentVersion(PDO $pdo): int;

    /**
     * Set version to the new value
     * @param PDO $pdo
     * @param int $new_version
     * @return void
     * @internal param int $version
     */
    public function updateVersion(PDO $pdo, int $new_version);
}
