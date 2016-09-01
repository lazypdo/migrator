<?php
namespace Tests\Factory\Config;

use Migrator\Factory\Config\JSONConfigProvider;
use PHPUnit_Framework_TestCase;
use RuntimeException;

/**
 * Class JSONConfigProviderTest
 * @package Tests\Factory\Config
 */
class JSONConfigProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Can not find config file 'invalid path'
     */
    public function testInvalidPath()
    {
        $config_provider = new JSONConfigProvider("invalid path");
        $config_provider->getConfig('none');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage No configuration found for database 'invalid database name'
     */
    public function testNoConfigurationForDatabase()
    {
        $config_provider = new JSONConfigProvider($this->getStubPath());
        $config_provider->getConfig("invalid database name");
    }

    public function testReadConfig()
    {
        $config_stub_path = $this->getStubPath();
        $config_stub = json_decode(file_get_contents($config_stub_path), true);

        foreach ($config_stub as $db_name => $stub) {
            $config_provider = new JSONConfigProvider($config_stub_path);
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
        return __DIR__ . '/../../stubs/config/stub.json';
    }
}
