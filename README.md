# Migrator is a simple database migration tool for PHP
[![Total Downloads](https://img.shields.io/packagist/dt/lazypdo/migrator.svg)](https://packagist.org/packages/lazypdo/migrator)
[![Latest Stable Version](https://img.shields.io/packagist/v/lazypdo/migrator.svg)](https://packagist.org/packages/lazypdo/migrator)
[![Travis Build](https://travis-ci.org/lazypdo/migrator.svg?branch=master)](https://travis-ci.org/lazypdo/migrator)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/c44976d6-9726-4aae-a423-865211cbc5b2.svg)](https://insight.sensiolabs.com/projects/c44976d6-9726-4aae-a423-865211cbc5b2)

The goal of this project is to create a simple yet modular database migration tool 
which would be easy to integrate with your project or to use standalone.

## Installation
Use composer: `composer require lazypdo/migrator`

## Migrations
Migrations is a set of SQL files residing in a dedicated directory.

### Naming
Every migration is a SQL file. The naming convention is the following:
`<version>.<direction>.<memo>.sql`

- _version_: a natural (positive integer) number
- _direction_: either "up" or "down"
- _memo_: optional text

Examples:
- `0004.up.create_foo_table.sql`
- `042.down.sql`

It is recommended to put a few leading zeros to make the migrations appear nicely sorted in file managers.

### Versions
* File `<N>.up.sql` defines the migration **from N-1 to N**. 
* File `<N>.down.sql` defines the migration **from N back to N-1**.

Versioning starts with 1. Every consequential upward migration must take the next natural number. 
When it is not possible to create a corresponding downward migration, the entire file must be omitted. 
E.g. if "42.up.sql" exists, but "42.down.sql" does not, Migrator will only be able to go from 41 to 42 but never back.

## Using Migrator standalone

Configuration is pretty straightforward. 
Create a configuration file "migrator.php":

```php
<?php
return [
    'my_database' => [
        'dsn' => 'pgsql:host=localhost;port=5432;dbname=testdb',
        'user' => 'my-database_user',
        'password' => 'my_database_password',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ],
        'migrations' => '/path/to/migrations'
    ]
];
```

Parameters _user_, _password_, and _options_ are optional.

You can add as many databases as you want. The configuration file must
either be in the current directory or specified using `--config` option:

`./migrator --config=/path/to/migrator.php status`

It is also possible to use json configs.

Migrator has just two commands: _status_ and _migrate_.

### Status
Shows the current status of the given database. The default database
name is "default". It shows the current versions and possible migration
range: the minimal and maximum version possible to migrate to.

`./migrator my_database status`

### Migrate
Migrates the database to the given target version.

`./migrator my_database migrate [version]`

* _version_: target version. If omitted, the highest possible version will be used.

## Using Migrator in your project
Your project might already have its configuration infrastructure.
You can tailor Migrator to your needs in just two steps.

### 1. Implement \Migrator\Factory\FactoryInterface
This is what gives the console application an instance of Migrator for
a given database name.

```php
class MyMigratorFactory implements \Migrator\Factory\FactoryInterface
{
    /**
     * Get an instance of Migrator for the given config
     * @param string $name
     * @return \Migrator\Migrator
     */
    public function getMigrator($name)
    {
        // Here you will need 3 components
        
        /* @var PDO */
        $pdo = ...;// Get it from your config according to the $name
        
        /* @var \Migrator\MigrationReaderInterface */
        $migrationReader = ...; // Use \Migrator\MigrationReader\SingleFolderCallbackMigrationReader or create your own
        
        /* @var \Migrator\VersionLogInterface */
        $log = ...; // Use \Migrator\VersionLog\DatabaseLog or create your own
        
        return new \Migrator\Migrator($pdo, $migrationReader, $log);
    }
}
```

### 2. Create your executable
Create a file called `my_migrator` in your project's bin directory.

```php
#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = new \Migrator\Console\Application(
    new MyMigratorFactory()
);
$app->run();
```

Make it executable: `chmod +x my_migrator`.

Done.
