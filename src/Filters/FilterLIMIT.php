<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

use RedBean_Facade as R;
use RedBean_QueryWriter_Oracle;

class FilterLIMIT implements FilterInterface
{

    public function validate(array $parameters)
    {
        if (!array_key_exists('value', $parameters)) {
            throw new \InvalidArgumentException("LIMIT expects a [limit] value.");
        }
    }

    public function apply(&$sql_reference, array &$values_reference, array $parameters)
    {
        $writer = R::$toolbox->getWriter();
        $values_reference[] = $parameters['value'];
        if ($writer instanceof RedBean_QueryWriter_Oracle) { // use oracle ROWNUM + subqueries as a polyfill
            $sql_reference = "SELECT * FROM  (
                SELECT VIRTUAL.*, ROWNUM ROWOFFSET FROM  (
                    {$sql_reference}
                ) VIRTUAL
            ) where ROWNUM <= ?";

            return;
        }
        $sql_reference .= " LIMIT ? ";
    }
}
