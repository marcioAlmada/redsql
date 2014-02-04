<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

class FilterOR extends NonArgumentedFilter
{
    public function apply(&$sql_reference)
    {
        $sql_reference .= " OR ";
    }
}
