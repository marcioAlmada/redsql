<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

abstract class GenericFilter implements FilterInterface
{

    protected $operator;

    public function validate(array $parameters)
    {
        if (!array_key_exists('field', $parameters) || !array_key_exists('value', $parameters)) {
            $token = str_replace('Filter', '', get_called_class());
            throw new \InvalidArgumentException("{$token} expects parameters to have a field and a value.");
        }
    }

    public function apply(&$sql_reference, array &$values_reference, array $parameters)
    {
        $sql_reference .= " {$parameters['field']} {$this->operator} ? ";
        $values_reference[] = $parameters['value'];
    }
}
