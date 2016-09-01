<?php
namespace Migrator\Factory\Config;

use RuntimeException;

class JSONConfigProvider extends BaseFileConfigProvider
{
    /**
     * @param string $db_name
     *
     * @return array
     */
    public function getConfig($db_name)
    {
        $this->validateFilePath();

        $config = json_decode(file_get_contents($this->file), true);
        if (isset($config[$db_name]) && is_array($config[$db_name])) {
            return $config[$db_name];
        }

        throw new RuntimeException("No configuration found for database '{$db_name}'");
    }
}
