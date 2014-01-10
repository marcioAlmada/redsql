<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

abstract class NonArgumentedFilter implements NonArgumentedFilterInterface
{
    public function validate() {}

    abstract public function apply(&$sql_reference);
}
