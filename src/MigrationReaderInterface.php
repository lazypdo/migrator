<?php
namespace Migrator;

interface MigrationReaderInterface
{
    /**
     * Does upgrade to the version exist?
     * @param int $version
     * @return bool
     */
    public function upgradeExistsTo($version);

    /**
     * Does downgrade from the version exist?
     * @param int $version
     * @return bool
     */
    public function downgradeExistsFrom($version);

    /**
     * Get SQL upgrading from ($version - 1) to $version
     * @param int $version
     * @return string
     * @throws \OutOfBoundsException if upgrade to version is not possible
     */
    public function getUpgradeTo($version);

    /**
     * Get SQL downgrading from ($version) to ($version - 1)
     * @param int $version
     * @return string
     * @throws \OutOfBoundsException if downgrade from version is not possible
     */
    public function getDowngradeFrom($version);
}
