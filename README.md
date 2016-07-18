# Migrator is a simple database migration tool for PHP
The goal of this project is to create a simple yet modular database migration tool 
which would be easy to integrate with your project or to use standalone.

## Installation
Use composer: `composer require lazypdo/migrator`

## Migrations
Migrations is a set of SQL files residing in a dedicated directory.

### Naming
Every migration is a SQL file. The file name name consists of
- version, a natural (positive integer) number
- direction, either "up" or "down"
- optional memo
- "sql" extension

Examples:
- 0004.up.create_foo_table.sql
- 1.down.sql

It is recommended to put a few leading zeros to make the migrations appear nicely sorted in file managers.

### Versions
* A file **N.up.sql** defines the migration **from N-1 to N**. 
* A file **N.down.sql** defines the migration **from N back to N-1**.

Versions must start with 1. Every consequential upward migration must take the next natural number. 
When it is not possible to create a corresponding downward migration, it must be omitted. 
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

Migrator has just two commands: status and migrate.

### Status
Shows the current status of the given database. The default database
name is "default". It shows the current versions and possible migration
range: the minimal and maximum version possible to migrate to.

`./migrator my_database status`

### Migrate
Migrates the database to the given target version.

`./migrator my_database migrate`

## Using Migrator in your project
Your project might already have its configuration infrastructure.
You can tailor Migrator to your needs in just three steps.

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
        $pdo = ...;// get it from your config according to the $name
        
        /* @var \Migrator\MigrationReaderInterface */
        $migrationReader = ...; // Use \Migrator\MigrationReader\SingleFolderCallbackMigrationReader or create your own
        
        /* @var \Migrator\VersionLogInterface */
        $log = ...; // Use \Migrator\VersionLog\DatabaseLog or create your own
        
        return new \Migrator\Migrator($pdo, $migrationReader, $log);
    }
}
```

### 2. Extend \Migrator\Console\Application
In your custom application, set the factory in the constructor.

```php
class MyMigratorApplication extends \Migrator\Console\Application
{
    public function __construct()
    {
        parent::__construct();
        $this->setFactory(new MyMigratorFactory());
    }
}
```

### 3. Create your binary
Create a file called `my_migrator` in your project's bin directory.

```php
#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = new MyMigratorApplication();
$app->run();
```

Make it executable: `chmod +x my_migrator`.

You're done.
