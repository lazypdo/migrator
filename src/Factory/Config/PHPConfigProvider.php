<?php
namespace Migrator\Factory\Config;

use RuntimeException;

class PHPConfigProvider extends BaseFileConfigProvider
{
    /**
     * @param string $db_name
     *
     * @return array
     */
    public function getConfig($db_name)
    {
        $this->validateFilePath();

        $config = $this->readConfig();
        if (is_array($config) && isset($config[$db_name]) && is_array($config[$db_name])) {
            return $config[$db_name];
        }

        throw new RuntimeException("No configuration found for database '{$db_name}'");
    }

    /**
     * @return mixed
     */
    private function readConfig()
    {
        return require $this->file;
    }
}
