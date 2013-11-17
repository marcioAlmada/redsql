<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

use R;

class FilterIN extends AbstractFilter
{
    public function apply(&$sql_reference, array &$values_reference, $field = null, $values = null)
    {
        if (!is_array($values)) {
            throw new \InvalidArgumentException("IN expects array of values for comparison.");
        }
        if (count($values)) {
            $sql_reference .= " {$field} IN (".R::genSlots($values).") ";
            $values_reference = $values_reference + $values;
        }
    }
}
