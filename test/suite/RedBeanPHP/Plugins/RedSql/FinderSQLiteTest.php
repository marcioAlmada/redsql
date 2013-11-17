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

    /**
     * @test
     */
    public function allowFindWithLimitAndOffset()
    {
        $this->assertCount(0, R::redsql('genius')->find(-1, count($this->data)));
        $this->assertCount(count($this->data), R::redsql('genius')->find(-1));
        $this->assertCount(count($this->data), R::redsql('genius')->find(-1, 0));
        $this->assertCount(count($this->data), R::redsql('genius')->find(-1, -1));
        $this->assertCount(count($this->data) - 2, R::redsql('genius')->find(-1, 2));

        parent::allowFindWithLimitAndOffset();
    }

}
