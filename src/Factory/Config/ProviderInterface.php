<?php
namespace Migrator\Factory\Config;

interface ProviderInterface
{
    /**
     * Get configuration for the database as array
     * Expected array structure:
     * [
     *      'dsn' => (string) PDO DSN
     *      'user' => (string) database username
     *      'password' => (string) database password
     *      'options' => (array) PDO connection options
     *      'migrations' => (string) folder containing migrations
     *      'version_log_class' => (sting) class implementing \Migrator\VersionLogInterface
     * ]
     * @param string $db_name
     * @return array
     */
    public function getConfig($db_name);
}
