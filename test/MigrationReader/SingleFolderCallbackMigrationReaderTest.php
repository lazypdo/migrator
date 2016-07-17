<?php
namespace Tests\MigrationReader;

use Migrator\MigrationReader\SingleFolderCallbackMigrationReader;
use OutOfBoundsException;
use PHPUnit_Framework_TestCase;

class SingleFolderCallbackMigrationReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage Version not found
     */
    public function testInvalidUpgradeVersion()
    {
        $reader = new SingleFolderCallbackMigrationReader(__DIR__ . '/../migrations');
        $reader->getUpgradeTo(42);
    }

    /**
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage Version not found
     */
    public function testInvalidDowngradeVersion()
    {
        $reader = new SingleFolderCallbackMigrationReader(__DIR__ . '/../migrations');
        $reader->getDowngradeFrom(42);
    }
}
