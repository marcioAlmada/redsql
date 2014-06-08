<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

class FilterCLOSE extends NonArgumentedFilter
{
    public function apply(&$sql_reference)
    {
        $sql_reference .= " ) ";
    }
}
