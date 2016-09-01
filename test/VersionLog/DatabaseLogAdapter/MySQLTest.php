<?php
namespace Tests\VersionLog\DatabaseLogAdapter;

use Migrator\VersionLog\DatabaseLogAdapter\MySQL;
use PDO;
use PDOException;

class MySQLTest extends \PHPUnit_Framework_TestCase
{
    private $pdo;

    private $mysqlObj;

    public function setUp()
    {
        if (!extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql extension is needed to run tests');
        }

        try {
            $this->pdo = new PDO(
                'mysql:dbname=' . DB_NAME . '; host=' . DB_HOST,
                DB_USER,
                DB_PASSWORD,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            $this->markTestSkipped(
                'this test using travis mysql with arg
                DB_NAME = mysql_test,
                DB_HOST = 127.0.0.1,
                DB_USER = root,
                DB_PASSWORD = "" '
            );
        }

        $this->mysqlObj = new MySQL();
    }

    public function versionDataProvider()
    {
        return [
            [0, 0],
            [21, 21],
            [54, 54],
            [78, 78],
        ];
    }

    /**
     * @param integer $version
     * @param integer $expected
     * @dataProvider versionDataProvider
     */
    public function testMySQLAdapter($version, $expected)
    {
        $this->assertNull($this->mysqlObj->init($this->pdo));

        $this->mysqlObj->updateVersion($this->pdo, $version);

        $this->assertEquals($expected, $this->mysqlObj->getCurrentVersion($this->pdo));
    }
}
