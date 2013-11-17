<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

abstract class NonArgumentedFilter implements FilterInterface
{
    public function validate(array $parameters)
    {
    }

    abstract public function apply(&$sql_reference, array &$values_reference, array $parameters);
}
