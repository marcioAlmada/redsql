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

    /**
     * @todo limit for oracle
     */
    public function apply(&$sql_reference, array &$values_reference, array $parameters)
    {
        $writer = R::$toolbox->getWriter();
        $values_reference[] = $parameters['value'];
        if ($writer instanceof RedBean_QueryWriter_Oracle) {
            $sql_reference .= " ROWNUM <= ? ";

            return;
        }
        $sql_reference .= " LIMIT ? ";
    }
}
