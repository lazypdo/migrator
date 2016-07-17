<?php
namespace Migrator\Factory;

interface FactoryInterface
{
    /**
     * Get an instance of Migrator for the given config
     * @param string $name
     * @return \Migrator\Migrator
     */
    public function getMigrator($name);
}
