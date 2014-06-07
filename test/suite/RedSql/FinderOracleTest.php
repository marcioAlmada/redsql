<?php

namespace RedBeanPHP\Plugins\RedSql;

use RedBean_Facade as R;

/**
 * @group oracle
 */
class OracleTest extends FinderTest
{

    public static function setUpBeforeClass()
    {
        R::setup('oracle:localhost:1521/xe','oracle','oracle');
    }
}
