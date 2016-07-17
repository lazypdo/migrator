<?php
namespace Tests\VersionLog\DatabaseLogAdapter;

class AbstractAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid table name
     */
    public function testTableNameException()
    {
        $this->getMockForAbstractClass('Migrator\\VersionLog\\DatabaseLogAdapter\\AbstractAdapter', ['incorrect table name']);
    }
}
