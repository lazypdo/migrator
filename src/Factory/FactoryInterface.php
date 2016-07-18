<?php
namespace Migrator\Factory;

interface FactoryInterface
{
    /**
     * Get an instance of Migrator for the given database
     * @param string $database
     * @return \Migrator\Migrator
     */
    public function getMigrator($database);
}
