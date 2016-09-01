<?php
namespace Migrator\Factory\Config;

use RuntimeException;

/**
 * Class BaseFileConfigProvider
 * @package Migrator\Factory\Config
 */
abstract class BaseFileConfigProvider implements ProviderInterface
{
    /**
     * @var string
     */
    protected $file;

    /**
     * AbstractFileConfigProvider constructor.
     *
     * @param string $file Path to config file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * @return bool
     */
    protected function isValidFilePath()
    {
        return (is_file($this->file) && is_readable($this->file));
    }

    /**
     * @throws RuntimeException
     */
    protected function validateFilePath()
    {
        if (!$this->isValidFilePath()) {
            throw new RuntimeException("Can not find config file '{$this->file}'");
        }
    }
}
