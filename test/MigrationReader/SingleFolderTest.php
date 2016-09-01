<?php
namespace Tests\MigrationReader;

use Migrator\MigrationReader\SingleFolder;
use OutOfBoundsException;
use PHPUnit_Framework_TestCase;

class SingleFolderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage Version not found
     */
    public function testInvalidUpgradeVersion()
    {
        $reader = new SingleFolder(__DIR__ . '/../stubs/migrations');
        $reader->getUpgradeTo(42);
    }

    /**
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage Version not found
     */
    public function testInvalidDowngradeVersion()
    {
        $reader = new SingleFolder(__DIR__ . '/../stubs/migrations');
        $reader->getDowngradeFrom(42);
    }
}
