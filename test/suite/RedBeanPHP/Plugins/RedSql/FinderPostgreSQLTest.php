<?php

namespace RedBeanPHP\Plugins\RedSql;

use RedBean_Facade as R;

/**
 * @group travis
 * @group postgre
 */
class FinderPostgreSQLTest extends FinderTest
{

    public function setUp()
    {
        R::setup('pgsql:host=localhost;dbname=redsql','postgres');
        parent::setup();
    }
}
