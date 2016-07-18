<?php
namespace Tests\VersionLog\DatabaseLogAdapter;

use Migrator\VersionLog\DatabaseLogAdapter\Factory;
use PHPUnit_Runner_Version;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    private $pdo;

    public function setUp()
    {
        if (PHPUnit_Runner_Version::id() < '5.0.0') {
            $this->markTestSkipped('PDO mocking is not possible');
        }
        $this->pdo = $this->getMockBuilder('PDO')
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
    }

    public function driverDataProvider()
    {
        return [
            ['sqlite', '\\Migrator\\VersionLog\\DatabaseLogAdapter\\SQLite'],
            ['sqlite2', '\\Migrator\\VersionLog\\DatabaseLogAdapter\\SQLite'],
            ['pgsql', '\\Migrator\\VersionLog\\DatabaseLogAdapter\\PostgreSQL'],
        ];
    }

    /**
     * @param string $driver_name
     * @param string $class
     * @dataProvider driverDataProvider
     */
    public function testGetAdapter($driver_name, $class)
    {
        $this->pdo->expects($this->once())
            ->method('getAttribute')
            ->with(\PDO::ATTR_DRIVER_NAME)
            ->willReturn($driver_name);

        $factory = new Factory();
        $this->assertInstanceOf($class, $factory->getAdapter($this->pdo));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Adapter for foo is not yet implemented
     */
    public function testGetAdapterUnknownDriver()
    {
        $this->pdo->expects($this->once())
            ->method('getAttribute')
            ->with(\PDO::ATTR_DRIVER_NAME)
            ->willReturn('foo');

        $factory = new Factory();
        $factory->getAdapter($this->pdo);
    }
}
