<?php
namespace Migrator\Factory\Config;

use RuntimeException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YAMLConfigProvider
 * @package Migrator\Factory\Config
 */
class YAMLConfigProvider extends BaseFileConfigProvider
{
    /**
     * @param string $db_name
     *
     * @return array
     */
    public function getConfig($db_name)
    {
        $this->validateFilePath();

        $config = Yaml::parse(file_get_contents($this->file));
        if (isset($config[$db_name]) && is_array($config[$db_name])) {
            return $config[$db_name];
        }

        throw new RuntimeException("No configuration found for database '{$db_name}'");
    }
}
