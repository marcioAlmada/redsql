<?php

namespace RedBeanPHP\Plugins\RedSql;

use R;

class FinderPostgreSQLTest extends FinderTest
{

    public function setUp()
    {
        R::setup('pgsql:host=localhost;dbname=redsql','postgres');
        parent::setup();
    }
}