RedSQL
=======

[![Build Status](https://travis-ci.org/marcioAlmada/redsql.png?branch=master)](https://travis-ci.org/marcioAlmada/redsql)
[![Coverage Status](https://coveralls.io/repos/marcioAlmada/redsql/badge.png?branch=master)](https://coveralls.io/r/marcioAlmada/redsql?branch=master)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/marcioAlmada/redsql/badges/quality-score.png?s=e5130c16fe66958344c76d632b96318525234af9)](https://scrutinizer-ci.com/g/marcioAlmada/redsql/)
[![Latest Stable Version](https://poser.pugx.org/redsql/redsql/v/stable.png)](https://packagist.org/packages/redsql/redsql)
[![Total Downloads](https://poser.pugx.org/redsql/redsql/downloads.png)](https://packagist.org/packages/redsql/redsql)
[![License](https://poser.pugx.org/redsql/redsql/license.png)](https://packagist.org/packages/redsql/redsql)

Programmatic and database agnostic SQL helper for RedBean delivered as a plugin.

# Features

- Express syntax
- Dynamic API: available methods are just a mirror of your table (bean) structure.
- Programmatic: avoid nasty string manipulations to achieve dynamic SQL construction.
- Database agnostic: SQL inconsistencies across databases (aka Oracle) like LIMIT and OFFSET are gracefully normalized.
- Lazy field loading: restrict wich fields you want to select.

## Installation

### With Composer
```json
{
  "require": {
    "redsql/redsql": "dev-master"
  }
}
```

Or just use your terminal: `composer require redsql/redsql:dev-master` :8ball:

### Phar (single file)

For the folks that are not using composer yet (why u no?) you can download
[redsql.phar](https://github.com/marcioAlmada/redsql/raw/master/dist/redsql.phar)
single distribution file and include it on your project:

```php
include 'path/to/redsql.phar';
```

## Usage

RedSQL public API is ~~awsome~~ fluid and completely achieved with PHP ~~wizardry~~ magic.
So, given the following table structure:

![table](https://dl.dropboxusercontent.com/u/49549530/redsql-project-table.png)

Express syntax:

```php
$projects =
    R::redsql('project')
        ->name('like', '%project x')
        ->priority('>', 5)
        ->created_at('between', [$time1, $time2])
        ->find($limit, $offset);
```

\+ Syntatic sugar:

```php
$projects =
    R::redsql('project')
        ->name('like', '%secret%')->AND->priority('>', 9)
        ->OR
        ->OPEN
            ->code('in', [007, 51])->AND->NOT->created_at('between', [$time1, $time2])
        ->CLOSE
        ->find($limit, $offset)
```

Select specific fields:

```php
$projects = R::redsql('project', ['name', 'description'])->find($limit, $offset);
//
SELECT `name`, `description` FROM `project` LIMIT ? OFFSET ?
```

\- Houston, we got Oracle! - No problem.

```php
$projects = R::redsql('project', ['name', 'description'])->find($limit, $offset);
//
SELECT * FROM  (
 SELECT VIRTUAL.*, ROWNUM ROWOFFSET FROM  (
   SELECT `name`, `description` FROM `project`
 ) VIRTUAL
) WHERE ROWNUM <= ? AND ROWOFFSET >= ?
```

## Database Support [![Build Status](https://travis-ci.org/marcioAlmada/redsql.png?branch=master)](https://travis-ci.org/marcioAlmada/redsql)

If build badge is green it means RedSql latest version is working on:

- Oracle
- Postgre
- MySQL
- SQLite
- CUBRID
- ~~MSSQL Server~~ (as soon as RedBean supports it)

## Roadmap

- [TODO] Beans collection
- [TODO] Relationships
~~ [DONE] ~~ Single file release (phar)

## Contributing
 
0. Fork redsql
0. Install composer dependencies `$ composer install --prefer-dist`
0. Run desired unit tests `$ phpunit` or at least `$ phpunit --group sqlite`
0. Modify code: correct bug, implement your awesome new feature
0. Back to step 3

When everything is ready, create a pull request to develop branch :)

PS: This plugin follows specification discussed in [#311](https://github.com/gabordemooij/redbean/issues/311).

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/marcioAlmada/redsql/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
