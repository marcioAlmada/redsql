RedSQL
=======

[![Build Status](https://travis-ci.org/marcioAlmada/redsql.png?branch=master)](https://travis-ci.org/marcioAlmada/redsql)
[![Coverage Status](https://coveralls.io/repos/marcioAlmada/redsql/badge.png?branch=master)](https://coveralls.io/r/marcioAlmada/redsql?branch=master)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/marcioAlmada/redsql/badges/quality-score.png?s=e5130c16fe66958344c76d632b96318525234af9)](https://scrutinizer-ci.com/g/marcioAlmada/redsql/)

Programmatic and database agnostic SQL helper for Redbean.

### Usage

```php
$projects =
    R::redsql('project')
        ->name('like', '%project x')
        ->priority('>', 5)
        ->created_at('between', [$time1, $time2])
        ->find();
```

\+ even more sugar:

```php
$projects =
    R::redsql('project')
        ->name('like', '%secret%')->AND->priority('>', 9)
        ->OR
        ->OPEN
            ->code('in', [007, 51])->AND->NOT->created_at('between', [$time1, $time2])
        ->CLOSE
        ->find()
```


### Installation

RedSql is not released yet and RedBean 4 plugin integration is still being elaborated. But if you really want to use it in a project right now you can just:

```php
include 'path/to/vendor/redsql/bootstrap.php';
```

OR register `RedSql\Finder` yourself:

```php
R::ext( 'redsql', function ($type) {
    return new RedBeanPHP\Plugins\RedSql\Finder($type);
});
```

### Contributing
 
0. Fork redsql
0. Clone your forked repository
0. Install composer dependencies `$ composer install --prefer-dist`
0. Setup redbean `$ cd vendor/gabordemooij/redbean/ && php replica.php && cd -`
0. Run unit tests `$ phpunit`
0. Modify code: correct bug, implement features
0. Back to step 5

When everything is ready, create a pull request to desenv branch :)

PS: This plugin follows specification discussed in [#311](https://github.com/gabordemooij/redbean/issues/311).