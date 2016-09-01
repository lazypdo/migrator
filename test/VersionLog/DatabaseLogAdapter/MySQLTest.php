<?php
namespace Tests\VersionLog\DatabaseLogAdapter;

use Migrator\VersionLog\DatabaseLogAdapter\MySQL;
use PDO;
use PDOException;

class MySQLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var MySQL
     */
    private $mysqlAdapter;

    public function setUp()
    {
        if (!extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql extension is not loaded');
        }

        try {
            /**
             * MYSQL_TEST_DB_* constants are defined in phpunut.xml.dist
             */
            $this->pdo = new PDO(
                sprintf('mysql:dbname=%s;host=%s', MYSQL_TEST_DB_NAME, MYSQL_TEST_DB_HOST),
                MYSQL_TEST_DB_USER,
                MYSQL_TEST_DB_PASSWORD,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            $this->markTestSkipped(
                sprintf(
                    'Exception: %s. Set MYSQL_TEST_DB_* constants in phpunit.xml to connect to a local database',
                    $e->getMessage()
                )
            );
        }

        $this->mysqlAdapter = new MySQL();
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
        $this->assertNull($this->mysqlAdapter->init($this->pdo));
        $this->mysqlAdapter->updateVersion($this->pdo, $version);
        $this->assertEquals($expected, $this->mysqlAdapter->getCurrentVersion($this->pdo));
    }
}
