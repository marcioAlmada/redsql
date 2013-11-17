<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

class FilterOR extends AbstractFilter
{
    public function apply(&$sql_reference, array &$values_reference, $field = null, $value = null)
    {
        $sql_reference .= " OR ";
    }
}
