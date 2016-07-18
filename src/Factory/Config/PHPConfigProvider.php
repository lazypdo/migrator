<?php
namespace Migrator\Factory\Config;

use RuntimeException;

class PHPConfigProvider implements ProviderInterface
{
    /**
     * @var string
     */
    private $file;

    /**
     * JSONConfigProvider constructor.
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * @param string $db_name
     * @return array
     */
    public function getConfig($db_name)
    {
        if (!is_file($this->file) || !is_readable($this->file)) {
            throw new RuntimeException("Can not find config file '{$this->file}'");
        }
        $config = $this->readConfig();
        if (is_array($config) && isset($config[$db_name]) && is_array($config[$db_name])) {
            return $config[$db_name];
        }
        throw new RuntimeException("No configuration found for database {$db_name}");
    }

    /**
     * @return mixed
     */
    private function readConfig()
    {
        return require $this->file;
    }
}
