<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

class FilterOPEN extends NonArgumentedFilter
{
    public function apply(&$sql_reference, array &$values_reference, array $parameters)
    {
        $sql_reference .= " ( ";
    }
}
