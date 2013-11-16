<?php

$loader = require __DIR__.'/vendor/autoload.php';

# loading redsql into R
R::ext( 'redsql', function ($type) {
    return new RedBeanPHP\Plugins\RedSql\Finder($type);
});
