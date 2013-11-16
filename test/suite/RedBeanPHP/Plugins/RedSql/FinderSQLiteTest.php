<?php

namespace RedBeanPHP\Plugins\RedSql;

use R;

class FinderSQLiteTest extends FinderTest
{

    public function setUp()
    {
        R::setup('sqlite:/tmp/redsql.db');
        parent::setup();
    }

}