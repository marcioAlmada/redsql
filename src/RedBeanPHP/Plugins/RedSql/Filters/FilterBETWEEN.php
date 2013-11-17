<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

class FilterBETWEEN extends GenericFilter
{

    protected $operator = 'BETWEEN';

    public function validate(array $parameters)
    {
        parent::validate($parameters);
        if (2 != count($parameters['value'])) {
            throw new \InvalidArgumentException("BETWEEN expects two values for comparison.");
        }
    }

    public function apply(&$sql_reference, array &$values_reference, array $parameters)
    {
        $sql_reference .= " {$parameters['field']} {$this->operator} ? AND ? ";
        $values_reference = $values_reference + $parameters['value'];
    }
}
