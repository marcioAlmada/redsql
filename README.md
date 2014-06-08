RedSQL
=======

[![Build Status](https://travis-ci.org/marcioAlmada/redsql.png?branch=master)](https://travis-ci.org/marcioAlmada/redsql)
[![Coverage Status](https://coveralls.io/repos/marcioAlmada/redsql/badge.png?branch=master)](https://coveralls.io/r/marcioAlmada/redsql?branch=master)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/marcioAlmada/redsql/badges/quality-score.png?s=e5130c16fe66958344c76d632b96318525234af9)](https://scrutinizer-ci.com/g/marcioAlmada/redsql/)
[![Latest Stable Version](https://poser.pugx.org/redsql/redsql/v/stable.png)](https://packagist.org/packages/redsql/redsql)
[![Total Downloads](https://poser.pugx.org/redsql/redsql/downloads.png)](https://packagist.org/packages/redsql/redsql)
[![License](https://poser.pugx.org/redsql/redsql/license.png)](https://packagist.org/packages/redsql/redsql)

RedSQL is a database agnostic helper for RedBean delivered as a plugin.
It's a tiny, fun and predictable DSL to create SQL queries in a programmatic way.

# Features

- Express syntax
- Dynamic API: available methods are just a mirror of your table (bean) structure.
- Programmatic: avoid nasty string manipulations to achieve dynamic SQL construction.
- Database agnostic: SQL inconsistencies across databases (aka Oracle) like LIMIT and OFFSET are gracefully normalized.
- Lazy field loading: restrict wich fields you want to select.
- Still looks like SQL

## Installation

### With Composer
```json
{
  "require": {
    "redsql/redsql": "~1.0"
  }
}
```

Or just use your terminal: `composer require redsql/redsql:~1.0` :8ball:

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

// parses to:

SELECT * FROM `project`
WHERE `name` LIKE ?
    AND `priority` > ?
    AND `created_at` BETWEEN ? AND ?
LIMIT ? OFFSET ?
```

\+ Syntatic sugar:

```php
$projects =
    R::redsql('project')
        ->name('like', '%secret%')->AND->priority('>', 9)
        ->OR
        ->OPEN
            ->code('in', [007, 51])
            ->AND->NOT->created_at('between', [$time1, $time2])
        ->CLOSE
        ->find($limit, $offset)

// parses to:

SELECT * FROM `project`
WHERE `name` LIKE ? 
    AND `priority` > ? 
    OR (
        `code` IN (?,?)
        AND  NOT  `created_at` BETWEEN ? AND ?
    )
LIMIT ? OFFSET ?
```

Select specific fields:

```php
$projects = R::redsql('project', ['name', 'description'])->find($limit, $offset);

// parses to:

SELECT `name`, `description` FROM `project` LIMIT ? OFFSET ?
```

\- Houston, we got Oracle! - No problem.

```php
$projects = R::redsql('project', ['name', 'description'])->find($limit, $offset);

// parses to:

SELECT * FROM  (
    SELECT VIRTUAL.*, ROWNUM ROWOFFSET FROM  (
       SELECT `name`, `description` FROM `project`
    ) VIRTUAL
) WHERE ROWNUM <= ? AND ROWOFFSET >= ?
```

## Public API

### R::redsql($table, array $fields = [])

Returns a configured `RedBeanPHP\Plugins\RedSql\Finder` instance restricted to a given entity:

```php
$project_query = R::redsql('project');
```

New conditions can be queued lazily:

```php
$project_query->created_at('>', '2014-04-03 00:00:00');
$projects = $project_query->find(); // finally reach database
```

Finder can restrict what fields should be queried:

```php
$project_query = R::redsql('project', ['name', 'priority']);
//
SELECT `id`, `name`, `priority` FROM `project`
```

### find(*$limit = null, $offset = null*)

Fetches an array of `RedBean_OODBBean` instances from database:

```php
$projects = R::redsql('project')->NOT->priority(3)->find();
//
SELECT * FROM `project` WHERE  NOT  `priority` = ?
```

### findFirst()

Applies `LIMIT` and `ORDER BY ASC` statements and fetches a single `RedBean_OODBBean` instance from database:

```php
$project = R::redsql('project')->NOT->priority(3)->findFirst();
//
SELECT * FROM `project` WHERE NOT `priority` = ? ORDER BY `id` ASC LIMIT 1 OFFSET ?
```

### findLast()

Applies `LIMIT` and `ORDER BY DESC` statements and fetches a single `RedBean_OODBBean` instance from database:

```php
$project = R::redsql('project')->NOT->priority(3)->findLast();
//
SELECT * FROM `project` WHERE NOT `priority` = ? ORDER BY `id` DESC LIMIT 1 OFFSET ?
```

### findAlike(*array $conditions, $limit = null, $offset = null*)

Applies a batch of conditions and fetches an array of `RedBean_OODBBean` instances from database:

```php
$projects = R::redsql('project')->findAlike([
    'name' => 'X Project',
    'description' => '%secret%',
    'priority' => [1, 2, 3]
    ], $limit, $offset
);

//

SELECT *
FROM `project`
WHERE  `name` = ? 
    AND  `description` LIKE ?
    AND  `priority` IN (?,?,?)
LIMIT ?  OFFSET ?
```

### toString()
Finders `RedBeanPHP\Plugins\RedSql\Finder` can be converted to plain SQL using `(string)` casting or `toString` method:

```php
$project_finder = R::redsql('project');
echo (string) $project_finder;
$project_finder->NOT->priority(3);
echo $project_finder->toString();

// >

SELECT * FROM `project`
SELECT * FROM `project` WHERE  NOT  `priority` = ?
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

- [ ] RedBean ~4.0 support
- [ ] Beans collection
- [ ] Relationships
- [x] Single file release (phar)

## Contributing
 
0. Fork redsql
0. Clone redsql `git clone git@github.com:marcioAlmada/redsql.git`
0. Install composer dependencies `$ composer install --prefer-dist`
0. Run desired unit tests `$ phpunit` or at least `$ phpunit --group sqlite`
0. Correct bug, implement feature
0. Back to step 4

When everything is ready, create a pull request to master branch :)

## Building
 
0. Clone redsql `git clone git@github.com:marcioAlmada/redsql.git`
0. Install [kherge/php-box](https://github.com/kherge/php-box)
0. Run `$ box build`
0. A new phar will be available at `dist` folder

PS: This plugin follows specification discussed in [#311](https://github.com/gabordemooij/redbean/issues/311).