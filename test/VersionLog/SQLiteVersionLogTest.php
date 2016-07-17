<?php
namespace Tests\VersionLogTable;

use InvalidArgumentException;
use Migrator\VersionLog\DatabaseLogAdapter\SQLite;
use PHPUnit_Framework_TestCase;

class SQLiteVersionLogTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid table name
     */
    public function testInvalidTableName()
    {
        new SQLite('such bad name');
    }
}
