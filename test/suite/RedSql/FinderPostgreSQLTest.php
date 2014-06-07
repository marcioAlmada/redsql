<?php

namespace RedBeanPHP\Plugins\RedSql;

use RedBean_Facade as R;

/**
 * @group travis
 * @group postgre
 */
class FinderPostgreSQLTest extends FinderTest
{

    public static function setUpBeforeClass()
    {
        R::setup('pgsql:host=localhost;dbname=redsql','postgres');
    }
}
