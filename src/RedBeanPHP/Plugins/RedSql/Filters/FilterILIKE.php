<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

use R;
use RedBean_QueryWriter_PostgreSQL;

class FilterILIKE extends AbstractFilter
{
    public function apply(&$sql_reference, array &$values_reference, $field = null, $value = null)
    {
        $writer = R::$toolbox->getWriter();
        $values_reference[] = $value;
        if ($writer instanceof RedBean_QueryWriter_PostgreSQL) {
            $sql_reference .= " {$field} ILIKE ? ";

            return;
        }
        # fallback to databases that do not support ILIKE
        $sql_reference .= " UPPER({$field}) LIKE UPPER(?) ";
    }
}
