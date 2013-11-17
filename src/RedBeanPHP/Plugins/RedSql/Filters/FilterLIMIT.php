<?php

namespace RedBeanPHP\Plugins\RedSql\Filters;

use R;
use RedBean_QueryWriter_PostgreSQL;
use RedBean_QueryWriter_Oracle;
use RedBean_QueryWriter_MySQL;
use RedBean_QueryWriter_CUBRID;
use RedBean_QueryWriter_SQLiteT;

class FilterLIMIT extends AbstractFilter
{
    /**
     * @todo limit for oracle
     */
    public function apply(&$sql_reference, array &$values_reference, $field = null, $limit = null)
    {
        $writer = R::$toolbox->getWriter();
        $values_reference[] = $limit;
        if ($writer instanceof RedBean_QueryWriter_Oracle) {
            $sql_reference .= " ROWNUM <= ? ";
            return;
        }
        $sql_reference .= " LIMIT ? ";
    }
}
