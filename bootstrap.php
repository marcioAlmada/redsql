<?php

$redbean = class_exists('R') ? 'R' : 'RedBean_Facade';

$redbean::ext( 'redsql', function ($type, array $fields = []) {
    return new RedBeanPHP\Plugins\RedSql\Finder($type, $fields);
});

unset($redbean);