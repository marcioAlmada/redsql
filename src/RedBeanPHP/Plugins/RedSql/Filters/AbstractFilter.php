<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

abstract class AbstractFilter implements FilterInterface
{
    abstract public function apply(&$sql_reference, array &$values_reference, $field = null, $value = null);
}
