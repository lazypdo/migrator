<?php
namespace Tests;

use Exception;
use Migrator\MigrationReader\SingleFolder;
use Migrator\Migrator;
use Migrator\VersionLog\DatabaseLog;
use PDO;
use PHPUnit_Framework_TestCase;

class FunctionalTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var Migrator
     */
    private $migrator;

    public function setUp()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension is needed to run functional tests');
        }
        $this->pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $this->migrator = new Migrator(
            $this->pdo,
            new SingleFolder(__DIR__ . '/stubs/migrations'),
            new DatabaseLog()
        );
    }

    public function testInitialState()
    {
        $this->assertCount(0, $this->getTables());
        $this->assertEquals([0, 0, 4], $this->migrator->getVersionRange());
        $this->assertArrayHasKey('__version_log', $this->getTables());
    }

    public function versionsAndRanges()
    {
        return [
            [[ // gap in upgrades
                [1, [0, 1, 4], ['table_v1' => 'CREATE TABLE "table_v1"(id INT PRIMARY KEY)']],
                [2, [0, 2, 4], ['table_v2' => 'CREATE TABLE "table_v2"(id INT PRIMARY KEY)']],
                [3, [3, 3, 4], ['table_v2' => 'CREATE TABLE "table_v2"(id INT PRIMARY KEY, a TEXT)']],
                [4, [3, 4, 4], ['table_v4' => 'CREATE TABLE "table_v4"(id INT PRIMARY KEY, a TEXT)']],
                [3, [3, 3, 4], ['table_v2' => 'CREATE TABLE "table_v2"(id INT PRIMARY KEY, a TEXT)']],
            ]],
            [[ // there and back
                [1, [0, 1, 4], ['table_v1' => 'CREATE TABLE "table_v1"(id INT PRIMARY KEY)']],
                [2, [0, 2, 4], ['table_v2' => 'CREATE TABLE "table_v2"(id INT PRIMARY KEY)']],
                [1, [0, 1, 4], ['table_v1' => 'CREATE TABLE "table_v1"(id INT PRIMARY KEY)']],
                [0, [0, 0, 4], []],
            ]],
            [[ // same version
                [0, [0, 0, 4], []],
                [0, [0, 0, 4], []],
            ]],
        ];
    }

    /**
     * @dataProvider versionsAndRanges
     * @param array $data_set
     * @throws Exception
     */
    public function testMigrationAndRange(array $data_set)
    {
        foreach ($data_set as $parameters) {
            list($version, $range, $expectedTables) = $parameters;
            $this->migrator->migrateTo($version);
            $this->assertEquals($range, $this->migrator->getVersionRange());
            $tables = $this->getTables();
            unset($tables['__version_log']);
            $this->assertEquals($expectedTables, $tables);
        }
    }

    /**
     * @return array
     */
    private function getTables()
    {
        $tables = [];
        $select = $this->pdo->prepare("SELECT * FROM sqlite_master WHERE type='table'");
        $select->execute();
        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
            $tables[$row['name']] = $row['sql'];
        }
        return $tables;
    }
}
