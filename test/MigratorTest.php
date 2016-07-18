<?php
namespace Tests;

use Exception;
use Migrator\Migrator;
use OutOfRangeException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use PHPUnit_Runner_Version;

class MigratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $pdo;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $log;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $reader;

    /**
     * @var Migrator
     */
    private $migratorReal;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $migratorMock;

    public function setUp()
    {
        if (PHPUnit_Runner_Version::id() < '5.0.0') {
            $this->markTestSkipped('PDO mocking is not possible');
        }
        $this->pdo = $this->getMockBuilder('PDO')
            ->disableOriginalConstructor()
            ->getMock();

        $this->log = $this->getMockForAbstractClass('Migrator\\VersionLogInterface');
        $this->reader = $this->getMockForAbstractClass('Migrator\\MigrationReaderInterface');
        $this->migratorReal = new Migrator($this->pdo, $this->reader, $this->log);
        $this->migratorMock = $this->getMockBuilder('Migrator\\Migrator')
            ->setConstructorArgs([$this->pdo, $this->reader, $this->log])
            ->setMethods(['getVersionRange'])
            ->getMock();
    }

    public function versionRanges()
    {
        return [
            [
                [0, 0, 0], 0, [], [],
                [42, 42, 42], 42, [], [],
                [1, 2, 3], 2, [1, 2, 3], [3, 2],
                [42, 42, 42], 42, [1, 2, 3], [3, 2],
            ],
        ];
    }

    /**
     * @param array $expected
     * @param int   $version
     * @param array $upgrades
     * @param array $downgrades
     * @dataProvider versionRanges
     */
    public function testGetVersionRange(array $expected, $version, array $upgrades, array $downgrades)
    {
        $this->log->method('getCurrentVersion')
            ->willReturn($version);

        $this->reader->method('upgradeExistsTo')
            ->willReturnCallback(function ($v) use ($upgrades) {
                return in_array($v, $upgrades);
            });

        $this->reader->method('downgradeExistsFrom')
            ->willReturnCallback(function ($v) use ($downgrades) {
                return in_array($v, $downgrades);
            });

        $this->assertEquals($expected, $this->migratorReal->getVersionRange());
    }


    /**
     * @expectedException OutOfRangeException
     * @expectedExceptionMessage Target version out of range
     */
    public function testMigrateThrowsException()
    {
        $this->migratorMock->method('getVersionRange')
            ->willReturn([1, 2, 3]);

        $this->migratorMock->migrateTo(5);
    }

    public function upDown()
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @param $is_up
     * @expectedException Exception
     * @expectedExceptionMessage OMG
     * @dataProvider upDown
     */
    public function testMigrateRollsBack($is_up)
    {
        $this->migratorMock->method('getVersionRange')
            ->willReturn([1, 2, 3]);

        $query = 'my test query';
        if ($is_up) {
            $target = 3;
            $this->reader->method('getUpgradeTo')
                ->with(3)
                ->willReturn($query);
        } else {
            $target = 1;
            $this->reader->method('getDowngradeFrom')
                ->with(2)
                ->willReturn($query);
        }

        $this->pdo->expects($this->at(0))
            ->method('beginTransaction');
        $this->pdo->expects($this->at(1))
            ->method('exec')
            ->with($query)
            ->willThrowException(new Exception('OMG'));

        $this->pdo->expects($this->at(2))
            ->method('rollback');

        $this->migratorMock->migrateTo($target);
    }
}
