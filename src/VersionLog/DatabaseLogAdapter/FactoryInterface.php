<?php
namespace Migrator\VersionLog\DatabaseLogAdapter;

use PDO;

interface FactoryInterface
{
    public function getAdapter(PDO $pdo): AbstractAdapter;
}
