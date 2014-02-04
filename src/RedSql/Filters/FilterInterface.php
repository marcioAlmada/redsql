<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

interface FilterInterface
{
    public function validate(array $parameters);
    public function apply(&$sql_reference, array &$values_reference, array $parameters);
}
