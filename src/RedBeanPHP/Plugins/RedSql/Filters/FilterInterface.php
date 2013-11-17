<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

interface FilterInterface
{
    public function apply(&$sql_reference, array &$values_reference, $field = null, $value = null);
}
