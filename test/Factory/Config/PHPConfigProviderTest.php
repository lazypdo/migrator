<?php
namespace Tests\Factory\Config;

use Migrator\Factory\Config\PHPConfigProvider;
use PHPUnit_Framework_TestCase;
use RuntimeException;

/**
 * Class PHPConfigProviderTest
 * @package Tests\Factory\Config
 */
class PHPConfigProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Can not find config file 'invalid path'
     */
    public function testInvalidPath()
    {
        $config_provider = new PHPConfigProvider("invalid path");
        $config_provider->getConfig('none');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage No configuration found for database 'invalid database name'
     */
    public function testNoConfigurationForDatabase()
    {
        $config_provider = new PHPConfigProvider($this->getStubPath());
        $config_provider->getConfig("invalid database name");
    }

    public function testReadConfig()
    {
        $config_stub_path = $this->getStubPath();
        $config_stub = require $config_stub_path;

        foreach ($config_stub as $db_name => $stub) {
            $config_provider = new PHPConfigProvider($config_stub_path);
            $config = $config_provider->getConfig($db_name);

            $this->assertTrue(is_array($config));
            foreach ($stub as $k => $v) {
                $this->assertEquals($v, $config[$k]);
            }
        }
    }

    /**
     * @return string
     */
    private function getStubPath()
    {
        return __DIR__ . '/../../stubs/config/stub.php';
    }
}
