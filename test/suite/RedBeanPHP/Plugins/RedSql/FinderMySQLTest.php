<?php

namespace RedBeanPHP\Plugins\RedSql;

use RedBean_Facade as R;

class FinderMySQLTest extends FinderTest
{

    public function setUp()
    {
        R::setup('mysql:host=localhost;dbname=redsql','root');
        parent::setup();
    }
}
