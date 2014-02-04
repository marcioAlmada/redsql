<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

class FilterNOT extends NonArgumentedFilter
{
    public function apply(&$sql_reference)
    {
        $sql_reference .= " NOT ";
    }
}
