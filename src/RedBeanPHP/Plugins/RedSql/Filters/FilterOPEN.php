<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

class FilterOPEN extends NonArgumentedFilter
{
    public function apply(&$sql_reference)
    {
        $sql_reference .= " ( ";
    }
}
