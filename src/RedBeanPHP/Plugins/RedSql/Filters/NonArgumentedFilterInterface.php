<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

interface NonArgumentedFilterInterface
{
    public function validate();
    public function apply(&$sql_reference);
}
