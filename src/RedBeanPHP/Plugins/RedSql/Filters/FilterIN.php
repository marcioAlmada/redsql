<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

use RedBean_Facade as R;

class FilterIN extends GenericFilter
{

    protected $operator = 'IN';

    public function validate(array $parameters)
    {
        parent::validate($parameters);
        if (!is_array($parameters['value'])) {
            throw new \InvalidArgumentException("IN expects array of values for comparison.");
        }
        if(!count($parameters['value']))
        {
            return false;
        }
    }

    public function apply(&$sql_reference, array &$values_reference, array $parameters)
    {
        $values = $parameters['value'];
        $field = $parameters['field'];
        $sql_reference .= " {$field} {$this->operator} (".R::genSlots($values).") ";
        $values_reference = $values_reference + $values;
    }
}
