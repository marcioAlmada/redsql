<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

use RedBean_Facade as R;
use RedBean_QueryWriter_PostgreSQL;

class FilterILIKE extends GenericFilter
{
    protected $operator = 'ILIKE';

    public function apply(&$sql_reference, array &$values_reference, array $parameters)
    {
        $values_reference[] = $parameters['value'];
        $field = $parameters['field'];
        $writer = R::$toolbox->getWriter();
        if ($writer instanceof RedBean_QueryWriter_PostgreSQL) {
            $sql_reference .= " {$field} {$this->operator} ? ";

            return;
        }
        # fallback to databases that do not support ILIKE
        $sql_reference .= " UPPER({$field}) LIKE UPPER(?) ";
    }
}
