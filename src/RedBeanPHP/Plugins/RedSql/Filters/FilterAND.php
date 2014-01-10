<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

class FilterAND extends NonArgumentedFilter
{
    public function apply(&$sql_reference)
    {
        $sql_reference .= " AND ";
    }
}
