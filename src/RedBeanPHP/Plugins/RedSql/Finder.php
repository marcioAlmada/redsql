<?php

namespace RedBeanPHP\Plugins\RedSql;

use RedBean_QueryWriter_SQLiteT;
use RedBean_QueryWriter_PostgreSQL;
use RedBean_QueryWriter_MySQL;
use RedBean_QueryWriter_CUBRID;
use RedBean_QueryWriter_Oracle;

use R;

class Finder
{
    protected $type;

    protected $sql = '';

    protected $values = [];

    protected $map = [
        '='  => 'EQUALS',
        '!='  => 'NOTEQUALS',
        '<>'  => 'NOTEQUALS',
        '<'  => 'LESS',
        '>'  => 'GREATER',
        '>=' => 'GREATEROREQUALS',
        '<=' => 'LESSOREQUALS',
        'like' => 'LIKE',
        'ilike' => 'ILIKE',
        'in' => 'IN',
        'between' => 'BETWEEN'
    ];

    protected $writer;

    public function __construct($type)
    {
        R::dispense($type);
        $this->type = $type;
        $this->writer = R::$toolbox->getWriter();
    }

    public function find()
    {
        return R::find($this->type, $this->sql, $this->values);
    }

    public function __call($field, $arguments)
    {
        return $this->createConditionOrFail($field, $arguments);
    }

    public function __get($token)
    {
        return $this->invokeBehavior($token);
    }

    protected function createConditionOrFail($field, $arguments)
    {
        list($token, $values) = $this->solveConditionConstruct($arguments);
        $this->invokeBehavior($token, $field, $values);

        return $this;
    }

    protected function solveConditionConstruct($args)
    {
        if (1 === count($args)) {
            return ['=', $args[0]];
        }

        return [$args[0], $args[1]];
    }

    protected function invokeBehavior($token, $field = null, $values = [])
    {
        $callback = $this->solveBehaviorCallback($token);
        if (!$this->behaviorsExists($callback)) {
            throw new \RuntimeException("\"{$token}\" is not a valid construct");
        }
        call_user_func_array([$this, $callback], [$field, $values]);

        return $this;
    }

    protected function behaviorsExists($callback)
    {
        if (method_exists($this, $callback)) {
           return true;
        }

        return false;
    }

    protected function solveBehaviorCallback($token)
    {
        $real_token = $this->sanitizeToken($token);
        if (in_array($real_token, array_keys($this->map))) {
            $real_token = $this->map[$token];
        }

        return 'SQL_'.$real_token;
    }

    protected function sanitizeToken($token)
    {
        return strtoupper(preg_replace('/\s+/', '', $token));
    }

    protected function SQL_AND()
    {
        $this->sql .= " AND ";
    }

    protected function SQL_OR()
    {
        $this->sql .= " OR ";
    }

    protected function SQL_NOT()
    {
        $this->sql .= " NOT ";
    }

    protected function SQL_OPEN()
    {
        $this->sql .= " ( ";
    }

    protected function SQL_CLOSE()
    {
        $this->sql .= " ) ";
    }

    protected function SQL_EQUALS($field, $value)
    {
        $this->SQL_GenericOperator($field, '=', $value);
    }

    protected function SQL_NOTEQUALS($field, $value)
    {
        $this->SQL_GenericOperator($field, '!=', $value);
    }

    protected function SQL_GREATER($field, $value)
    {
        $this->SQL_GenericOperator($field, '>', $value);
    }

    protected function SQL_GREATEROREQUALS($field, $value)
    {
        $this->SQL_GenericOperator($field, '>=', $value);
    }

    protected function SQL_LESS($field, $value)
    {
        $this->SQL_GenericOperator($field, '<', $value);
    }

    protected function SQL_LESSOREQUALS($field, $value)
    {
        $this->SQL_GenericOperator($field, '<=', $value);
    }

    // protected function SQL_($field, $value)
    // {
    //     $this->SQL_GenericOperator($field, '!=', $value);
    //     $this->values[] = $value;
    // }

    protected function SQL_IN($field, array $values)
    {
        if (count($values)) {
            $this->sql .= " {$field} IN (".R::genSlots($values).") ";
            $this->values = $this->values + $values;
        }
    }

    protected function SQL_BETWEEN($field, array $values)
    {
        if (2 != count($values)) {
            throw new \InvalidArgumentException("BETWEEN expects two values for comparison.");
        }
        $this->sql .= " {$field} BETWEEN ? AND ? ";
        $this->values = $this->values + $values;
    }

    protected function SQL_LIKE($field, $value)
    {
        $this->SQL_GenericOperator($field, 'LIKE', $value);
    }

    protected function SQL_ILIKE($field, $value)
    {
        $this->values[] = $value;
        if ($this->writer instanceof RedBean_QueryWriter_PostgreSQL || $this->writer instanceof RedBean_QueryWriter_MySQL) {
            $this->sql .= " {$field} ILIKE ? ";
            return;
        }
        # fallback to databases that do not support ILIKE
        $this->sql .= " UPPER({$field}) LIKE UPPER(?) ";
    }

    protected function SQL_GenericOperator($field, $token, $value)
    {
        $this->sql .= " {$field} {$token} ? ";
        $this->values[] = $value;
    }
}
