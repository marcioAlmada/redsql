<?php

namespace RedBeanPHP\Plugins\RedSql;

use R;

class FinderMySQLTest extends FinderTest
{

    public function setUp()
    {
        R::setup('mysql:host=localhost;dbname=redsql','root');
        parent::setup();
    }
}