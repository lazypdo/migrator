<?php
namespace Migrator\MigrationReader;

use DirectoryIterator;
use Migrator\MigrationReaderInterface;
use OutOfBoundsException;

class SingleFolder implements MigrationReaderInterface
{
    /**
     * @var array
     */
    private $up = [];

    /**
     * @var array
     */
    private $down = [];

    /**
     * SingleFolderMigrationReader constructor.
     * @param string $folder Folder to read from
     */
    public function __construct($folder)
    {
        foreach (new DirectoryIterator($folder) as $file) {
            if ($file->isDot()) {
                continue;
            }
            if ($this->match($file->getFilename(), $version, $is_upgrade)) {
                $pathname = $file->getPathname();
                if ($is_upgrade) {
                    $this->up[(int) $version] = $pathname;
                } else {
                    $this->down[(int) $version] = $pathname;
                }
            }
        }
    }

    /**
     * Checks if the filename matches the naming convention.
     * If matches, sets $version and $direction variables
     * @param string $filename
     * @param int    $version Matched version number
     * @param bool   $is_upgrade True if upgrade, false if downgrade
     * @return bool
     */
    public function match($filename, &$version, &$is_upgrade)
    {
        if (preg_match('/^(?<version>\d+)\.(?<dir>up|dn|down)(\..+)?\.sql$/', $filename, $match)) {
            $version = (int) $match['version'];
            $is_upgrade = ($match['dir'] === 'up');
            return true;
        }
        return false;
    }

    /**
     * Does upgrade to the version exist?
     * @param int $version
     * @return bool
     */
    public function upgradeExistsTo($version)
    {
        return array_key_exists($version, $this->up);
    }

    /**
     * Does downgrade from the version exist?
     * @param int $version
     * @return bool
     */
    public function downgradeExistsFrom($version)
    {
        return array_key_exists($version, $this->down);
    }

    /**
     * Get SQL upgrading from ($version - 1) to $version
     * @param int $version
     * @return string
     * @throws OutOfBoundsException if upgrade to version is not possible
     */
    public function getUpgradeTo($version)
    {
        return $this->getContents($this->up, $version);
    }

    /**
     * Get SQL downgrading from ($version) to ($version - 1)
     * @param int $version
     * @return string
     * @throws OutOfBoundsException if downgrade from version is not possible
     */
    public function getDowngradeFrom($version)
    {
        return $this->getContents($this->down, $version);
    }
    
    private function getContents(array $files, $key)
    {
        if (array_key_exists($key, $files)) {
            return file_get_contents($files[$key]);
        }
        throw new OutOfBoundsException('Version not found');
    }
}
