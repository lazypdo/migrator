<?php
namespace Migrator\Factory\Config;

use RuntimeException;

class JSONConfigProvider implements ProviderInterface
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
            throw new RuntimeException("Can not find config file. Please make sure '{$this->file}' is within \$PATH.");
        }
        $config = json_decode(file_get_contents($this->file), true);
        if (isset($config[$db_name]) && is_array($config[$db_name])) {
            return $config[$db_name];
        }
        throw new RuntimeException("No configuration found for database {$db_name}");
    }
}
