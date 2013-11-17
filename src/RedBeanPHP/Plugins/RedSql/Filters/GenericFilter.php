<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

class GenericFilter extends AbstractFilter
{

    protected $operator;

    public function apply(&$sql_reference, array &$values_reference, $field = null, $value = null)
    {
        $sql_reference .= " {$field} {$this->operator} ? ";
        $values_reference[] = $value;
    }
}
