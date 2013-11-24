RedSQL
=======

[![Build Status](https://travis-ci.org/marcioAlmada/redsql.png?branch=master)](https://travis-ci.org/marcioAlmada/redsql)
[![Coverage Status](https://coveralls.io/repos/marcioAlmada/redsql/badge.png?branch=master)](https://coveralls.io/r/marcioAlmada/redsql?branch=master)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/marcioAlmada/redsql/badges/quality-score.png?s=e5130c16fe66958344c76d632b96318525234af9)](https://scrutinizer-ci.com/g/marcioAlmada/redsql/)

Programmatic and database agnostic SQL helper for Redbean delivered as a plugin.

# Features

* Express syntax
* Dynamic API: available methods are just a mirror of your table (bean) structure.
* Programmatic: Avoid nasty string manipulations to achieve dynamic SQL construction.
* Database agnostic: API inconsistencies across databases (aka Oracle) are gracefully normalized.
* Does not aims to hide SQL (too much).

## Installation

RedBean plugin delivery process is still under discussion, but if you really want to use it in a project right now you can manually update `composer.json` with:

```json
{
  "require": {
    "redsql/redsql": "dev-master"
  }
}
```

Or just use your terminal: `composer require redsql/redsql:dev-master` :8ball:

## Usage

RedSQL public API is fluid and completely achieved with magic methods. Given the following table structure:

<table>
  <tr>
    <th>id</th><th>name</th><th>priority</th><th>created_at</th>
  </tr>
</table>


```php
$projects =
    R::redsql('project')
        ->name('like', '%project x')
        ->priority('>', 5)
        ->created_at('between', [$time1, $time2])
        ->find($limit, $offset);
```

\+ some syntatic sugar:

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


## Database Support [![Build Status](https://travis-ci.org/marcioAlmada/redsql.png?branch=master)](https://travis-ci.org/marcioAlmada/redsql)

If build badge is green it means RedSql latest version is working on:

- Postgre
- MySQL
- SQLite
- CUBRID
- ~~Oracle~~ (soon)
- ~~MSSQL Server~~ (as soon as RedBean supports it)

## Contributing
 
0. Fork redsql
0. Clone your forked repository
0. Install composer dependencies `$ composer install --prefer-dist`
0. Setup redbean `$ cd vendor/gabordemooij/redbean/ && php replica.php && cd -`
0. Run desired unit tests `$ phpunit` or at least `$ phpunit test/suite/RedBeanPHP/Plugins/RedSql/FinderSQLiteTest.php`
0. Modify code: correct bug, implement features
0. Back to step 5

When everything is ready, create a pull request to develop branch :)

PS: This plugin follows specification discussed in [#311](https://github.com/gabordemooij/redbean/issues/311).