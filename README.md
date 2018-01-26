# MySQLBackup

- [Installation](#installation)
- [Initialization](#initialization)
- [Usage](#usage)
    - [Add tables](#add-tables)
	- [Exclude tables](#exclude-tables)
	- [Set the filename](#set-the-filename)
	- [Do not dump the table schema or the datas](#do-not-dump-the-table-schema-or-the-datas)
    - [Do not add CREATE DATABASE IF NOT EXISTS](#do-not-add-create-database-if-not-exists)
	- [Do not add DROP TABLE](#do-not-add-drop-table)
	- [Do not add IF NOT EXISTS](#do-not-add-if-not-exists)
	- [Compress file](#compress-file)
	- [Delete file](#delete-file)
	- [Auto download](#auto-download)
	- [Execute the backup](#execute-the-backup)
- [Full example](#full-example)


## Introduction

This is a backup utility used to dump a database for backup. The backup file contains the queries to build the tables and insert the datas.  
You can select some or all tables, compress the backup in ZIP or GZIP and start downloading automatically.


## Installation

With Composer, add this line to your *require* section :

	"phelium/mysql-backup": "dev-master"

Then run `composer update`.


## Initialization

```php
require 'vendor/autoload.php';

use Phelium\Component\MySQLBackup;
```
	

To initialize MySQLBackup, you must provide your database information :  

```php
$Dump = new MySQLBackup('server name', 'user name', 'password', 'db name', 'mysql port');
```

MySQL port default is 3306.


## Usage

### Add tables

By default, all tables are saved. If you want to save one or more tables, the functions `addTable` or `addTables` must be used:

```php
$Dump = new MySQLBackup('localhost', 'root', '', 'blog');
$Dump->addTable('posts'); // Add the posts table
$Dump->addTables(array('users', 'comments')); // Add the users and comments tables
```


### Exclude tables

You can exclude some tables of the backup. Use `excludeTables` function. The parameter must be an array containing tables name to exclude.

```php
$Dump = new MySQLBackup('localhost', 'root', '', 'blog');
$Dump->excludeTables(array(comments')); // Exclude comments table to the backup
```



### Set the filename

By default, the name of the generated file is "dump\_{database name}\_{date}". With `setFilename` method, you can specify a different file name:

```php
$Dump = new MySQLBackup('localhost', 'root', '', 'blog');
$Dump->setFilename('bkp_'.time());
```


### Do not dump the table schema or the datas

By default, the table schema (structure) and datas of each table are saved. However, it's possible not to save one or the other with `setDumpStructure` and `setDumpDatas` (default : true) :

```php
$Dump = new MySQLBackup('localhost', 'root', '', 'blog');
$Dump->setDumpStructure(false); // Not the structure
$Dump->setDumpDatas(false); // Not the datas
```


### Do not add CREATE DATABASE IF NOT EXISTS

By default, the *CREATE DATABASE IF NOT EXISTS* directive is added in top of the backup file. You can disable it with `addCreateDatabaseIfNotExists` (default : true) :

```php
$Dump = new MySQLBackup('localhost', 'root', '', 'blog');
$Dump->addCreateDatabaseIfNotExists(false); // Not add the CREATE DATABASE IF NOT EXISTS statment
```


### Do not add DROP TABLE

By default, the *DROP TABLE IF EXISTS* directive is added before each CREATE TABLE. You can disable it with `addDropTable` (default : true) :

```php
$Dump = new MySQLBackup('localhost', 'root', '', 'blog');
$Dump->addDropTable(false); // Not add the DROP TABLE IF EXISTS statment
```


### Do not add IF NOT EXISTS

By default, the *IF NOT EXISTS* directive is added during a CREATE TABLE. You can disable it with `addIfNotExists` (default : true) :

```php
$Dump = new MySQLBackup('localhost', 'root', '', 'blog');
$Dump->addIfNotExists(false); // Not add the IF NOT EXISTS statment
```


### Compress file

To compress the output file, `setCompress` allows to use ZIP or GZIP (default : no compression) :

```php
$Dump = new MySQLBackup('localhost', 'root', '', 'blog');
$Dump->setCompress('zip'); // zip | gz | gzip
```


### Delete file

The generated file can be deleted automatically with `setDelete` (default : false) :

```php
$Dump = new MySQLBackup('localhost', 'root', '', 'blog');
$Dump->setDelete(true);
```


### Auto download

To download automatically the generated file, use `setDownload` (default : false) :

```php
$Dump = new MySQLBackup('localhost', 'root', '', 'blog');
$Dump->setDownload(true); // starts downloading
```


### Execute the backup

To start backing up your database, use `dump`:

```php
$Dump = new MySQLBackup('localhost', 'root', '', 'blog');
$Dump->dump();
```


# Full example

Here is an example to backup the *blog* database. Only the tables *posts*, *comments* and *users* are kept. Then compressed in ZIP format. The generated file is deleted from the server and the download starts automatically:

```php
<?php
require 'vendor/autoload.php';
    
use Phelium\Component\MySQLBackup;

$Dump = new MySQLBackup('localhost', 'root', '', 'blog');
$Dump->addTables(array('posts', 'comments', 'users'));
$Dump->setCompress('zip');
$Dump->setDelete(true);
$Dump->setDownload(true);
$Dump->dump();

// or

$Dump = new MySQLBackup('localhost', 'root', '', 'blog');
$Dump
    ->addTables(array('posts', 'comment', 'users'));
    ->setCompress('zip');
    ->setDelete(true);
    ->setDownload(true);
    ->dump();
```