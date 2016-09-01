<?php
namespace Tests\Factory\Config;

use Migrator\Factory\Config\YAMLConfigProvider;
use PHPUnit_Framework_TestCase;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YAMLConfigProviderTest
 * @package Tests\Factory\Config
 */
class YAMLConfigProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Can not find config file 'invalid path'
     */
    public function testInvalidPath()
    {
        $config_provider = new YAMLConfigProvider("invalid path");
        $config_provider->getConfig('none');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage No configuration found for database 'invalid database name'
     */
    public function testNoConfigurationForDatabase()
    {
        $config_provider = new YAMLConfigProvider($this->getStubPath());
        $config_provider->getConfig("invalid database name");
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unable to parse at line 1 (near "Hey").
     */
    public function testInvalidYAMLFile()
    {
        $config_provider = new YAMLConfigProvider($this->getInvalidStubPath());
        $config_provider->getConfig("something");
    }

    public function testReadConfig()
    {
        $config_stub_path = $this->getStubPath();
        $config_stub = Yaml::parse(file_get_contents($config_stub_path));

        foreach ($config_stub as $db_name => $stub) {
            $config_provider = new YAMLConfigProvider($config_stub_path);
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
        return __DIR__ . '/../../stubs/config/yaml/stub.yaml';
    }

    /**
     * @return string
     */
    private function getInvalidStubPath()
    {
        return __DIR__ . '/../../stubs/config/yaml/invalid.yaml';
    }
}
