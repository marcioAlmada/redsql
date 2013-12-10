<?php

namespace RedBeanPHP\Plugins\RedSql;

use RedBean_Facade as R;

class OracleTest extends FinderTest
{

    public function setUp()
    {
        R::setup('oracle:localhost:1521/xe','system','manager@');
        parent::setup();
    }
}
