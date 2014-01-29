<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

class FilterNOTEQUALS extends GenericFilter
{
    protected $operator = '!=';

    public function apply(&$sql_reference, array &$values_reference, array $parameters)
    {
        if(is_bool($parameters['value'])) {
            $parameters['value'] = (integer) $parameters['value'];
        }
        parent::apply($sql_reference, $values_reference, $parameters);
    }
}
