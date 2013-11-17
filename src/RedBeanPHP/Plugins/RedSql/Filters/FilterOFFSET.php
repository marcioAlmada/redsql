<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

use R;
use RedBean_QueryWriter_PostgreSQL;
use RedBean_QueryWriter_Oracle;
use RedBean_QueryWriter_MySQL;
use RedBean_QueryWriter_CUBRID;
use RedBean_QueryWriter_SQLiteT;

class FilterOFFSET extends AbstractFilter
{
    /**
     * @todo limit for oracle
     */
    public function apply(&$sql_reference, array &$values_reference, $field = null, $offset = null)
    {
        $writer = R::$toolbox->getWriter();
        $values_reference[] = $offset;
        if ($writer instanceof RedBean_QueryWriter_Oracle) {
            return;
        }
        $sql_reference .= " OFFSET ? ";
    }
}