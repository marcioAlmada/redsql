<?php

namespace RedBeanPHP\Plugins\RedSql;

use RedBean_Facade as R;

class Finder
{
    /**
     * Query writer
     *
     * @var RedBean_QueryWriter_AQueryWriter
     */
    protected $writer;

    /**
     * Bean type
     *
     * @var string
     */
    protected $type;

    /**
     * SQL string
     *
     * @var string
     */
    protected $sql = '';

    /**
     * SQL values
     *
     * @var array
     */
    protected $values = [];

    /**
     * Flag that informs if WHERE statement is already present.
     * Needs to be removed later.
     *
     * @var boolean
     */
    protected $where = false;

    /**
     * Allows express syntax, flagging finder to trigger AND operator automatically
     * @var boolean
     */
    protected $express = false;

    /**
     * Instances a new finder
     *
     * @param string $type   bean type
     * @param array  $fields fields to be selected
     */
    public function __construct($type, array $fields = [])
    {
        R::dispense($type);
        $this->type = $type;
        $this->writer = R::$toolbox->getWriter();
        $this->bootstrapSql($type, $fields);
        $this->turnExpressModeOff();
    }

    /**
     * Applies argumented filters
     *
     * @param string $field     database field name
     * @param mixed  $arguments arguments for filter
     */
    public function __call($field, $arguments)
    {
        return $this->applyFilterOrFail(
            $this->writer->esc($field),
            $arguments
        );
    }

    /**
     * Applies non argumented filters
     *
     * @param string $token token
     */
    public function __get($token)
    {
        return $this->applyFilter($token, []);
    }

    public function __toString()
    {
        return $this->sql .' -> '. json_encode($this->values);
    }

    /**
     * Triggers database interaction and brings results
     *
     * @param  integer $limit      SQL limit
     * @param  integer $offset     SQL offset
     * @param  string  $sql_append arbitrary sql snippets
     * @return array   array of RedBean_OODBBean
     */
    public function find($limit = null, $offset = null, $sql_append = '')
    {

        // backup state
        $sql = $this->sql;
        $values = $this->values;

        $this->sql .= " {$sql_append} ";
        $this->applyLimitAndOffset($limit, $offset);
        $rows = R::getAll($this->sql, $this->values);

        // restore state
        $this->sql = $sql;
        $this->values = $values;

        $this->turnExpressModeOn();

        return R::convertToBeans($this->type, $rows);
    }

    /**
     * Triggers database interaction and brings first match
     *
     * @return RedBean_OODBBean
     */
    public function findFirst($order = 'id')
    {
        $results = $this->find(1, 0, ' ORDER BY '. $this->writer->esc($order) .' ASC ');

        return reset($results);
    }

    /**
     * Triggers database interaction and brings last match
     *
     * @return RedBean_OODBBean
     */
    public function findLast($order = 'id')
    {
        $results = $this->find(1, null, ' ORDER BY ' . $this->writer->esc($order) . ' DESC ');

        return end($results);
    }

    /**
     * Triggers database interaction and brings last result
     *
     * @return RedBean_OODBBean
     */
    public function findAlike(array $conditions, $limit = null, $offset = null)
    {
        array_walk($conditions, function ($value, $field) {
            switch ( gettype($value) ) {
                case 'array':
                    $this->$field('IN', $value);
                    break;
                case 'string':
                    if ('' == trim($value)) {
                        break;
                    }
                    if ( false !== strpos($value, '%') ) {
                        $this->$field('ILIKE', $value);
                        break;
                    }
                default:
                    $this->$field($value);
                    break;
            }
        });

        return $this->find($limit, $offset);
    }

    /**
     * Applies LIMIT and OFFSET filters to SQL
     *
     * @param integer $limit  SQL limit
     * @param integer $offset SQL offset
     */
    protected function applyLimitAndOffset($limit = null, $offset = null)
    {
        if (null !== $limit) {
            $this->applyFilter('LIMIT', ['value' => $limit], true);
            if (null !== $offset) {
                $this->applyFilter('OFFSET', ['value' => $offset], true);
            }
        }
    }

    /**
     * Initializes finder SQL
     *
     * @param string $type   bean type
     * @param array  $fields fields to be selected
     */
    protected function bootstrapSql($type, array $fields = null)
    {
        if (!$fields) {
            $fields = ['*'];
        } else {
            array_unshift($fields, 'id');
            $fields = array_unique( array_map(function ($field) {
                return $this->writer->esc(strtolower($field));
            }, $fields));
        }
        $table = $this->writer->esc($type);
        $fields = implode($fields, ', ');
        $this->sql = "SELECT {$fields} FROM {$table}";
    }

    /**
     * Solves filter arguments and applies SQL filter
     *
     * @param  string            $field     database field name
     * @param  array             $arguments filter arguments
     * @return self
     * @throws \RuntimeException If Filter is not found or if filter arguments are not correct
     */
    protected function applyFilterOrFail($field, $arguments)
    {
        if ($this->isExpressModeOn()) { $this->AND; }
        list($token, $values) = $this->solveFilterArgs($arguments);
        $this->applyFilter($token, ['field' => $field, 'value' => $values]);
        $this->turnExpressModeOn();

        return $this;
    }

    /**
     * Solves filter arguments based on RedSql conventions
     *
     * @param  array $args filter arguments
     * @return array filter token and filter value ['<token>', '<value|s>']
     */
    protected function solveFilterArgs(array $args)
    {
        if (1 == count($args)) {
            return ['=', $args[0]];
        }

        return [$args[0], $args[1]];
    }

    /**
     * Applies SQl filter
     *
     * @param  string  $token      filter identifier
     * @param  array   $parameters filter arguments
     * @param  boolean $bypass     flag to bypass WHERE check
     * @return self
     */
    protected function applyFilter($token, array $parameters, $bypass = false)
    {
        $Filter = (new FilterResolver)->getFilterInstanceOrFail($token);
        if (false !== $Filter->validate($parameters)) {
            if (!$bypass && !$this->where) {
                $this->sql .= " WHERE ";
                $this->where = true;
            }
            $Filter->apply($this->sql, $this->values, $parameters);
            $this->turnExpressModeOff();
        }

        return $this;
    }

    /**
     * Enables express syntax
     */
    protected function turnExpressModeOn()
    {
        $this->express = true;
    }

    /**
     * Disables express syntax
     */
    protected function turnExpressModeOff()
    {
        $this->express = false;
    }

    /**
     * Checks if express syntax mode is active
     */
    protected function isExpressModeOn()
    {
        return $this->express;
    }

}
