<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

class FilterBETWEEN extends AbstractFilter
{
    public function apply(&$sql_reference, array &$values_reference, $field = null, $values = null)
    {
        if (2 != count($values)) {
            throw new \InvalidArgumentException("BETWEEN expects two values for comparison.");
        }
        $sql_reference .= " {$field} BETWEEN ? AND ? ";
        $values_reference = $values_reference + $values;
    }
}
