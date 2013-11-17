<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

use R;

class FilterIN extends GenericFilter
{

    protected $operator = 'IN';

    public function validate(array $parameters)
    {
        parent::validate($parameters);
        if (!is_array($parameters['value'])) {
            throw new \InvalidArgumentException("IN expects array of values for comparison.");
        }
    }

    public function apply(&$sql_reference, array &$values_reference, array $parameters)
    {
        $values = $parameters['value'];
        $field = $parameters['field'];
        if (count($values)) {
            $sql_reference .= " {$field} {$this->operator} (".R::genSlots($values).") ";
            $values_reference = $values_reference + $values;
        }
    }
}
