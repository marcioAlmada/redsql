<?php

namespace RedBeanPHP\Plugins\RedSql;

use RedBean_Facade as R;

class Finder
{
    /**
     * Query writer
     * @var RedBean_QueryWriter_AQueryWriter
     */
    protected $writer;

    /**
     * Bean type
     * @var string
     */
    protected $type;

    /**
     * SQL string
     * @var string
     */
    protected $sql = '';

    /**
     * SQL values
     * @var array
     */
    protected $values = [];

    protected $where = false;

    /**
     * Allows express syntax, flagging finder to trigger AND operator automatically
     * @var boolean
     */
    protected $express = false;

    public function __construct($type, array $fields = [])
    {
        R::dispense($type);
        $this->type = $type;
        $this->writer = R::$toolbox->getWriter();
        $this->bootstrapSql($type, $fields);
        $this->turnExpressModeOff();
    }

    public function __call($field, $arguments)
    {
        return $this->applyFilterOrFail($field, $arguments);
    }

    public function __get($token)
    {
        return $this->applyFilter($token, []);
    }

    public function __toString() {
        return $this->sql .' -> '. json_encode($this->values);
    }

    public function find($limit = null, $offset = null, $sql_append = '')
    {
        $this->sql .= " {$sql_append} ";
        $this->applyLimitAndOffset($limit, $offset);
        $rows =  R::getAll($this->sql, $this->values);

        return R::convertToBeans($this->type, $rows);
    }

    public function findFirst()
    {        
        $results = $this->find(1, 0, ' ORDER BY '. $this->writer->esc('id') .' ASC ');
        
        return reset($results);
    }

    public function findLast()
    {
        $results = $this->find(1, 0, ' ORDER BY ' . $this->writer->esc('id') . ' DESC ');
        
        return end($results);
    }

    public function findAlike(array $conditions, $limit = null, $offset = null) {
        array_walk($conditions, function($value, $field){
            switch ( gettype($value) ) {
                case 'array':
                    $this->$field('IN', $value);
                    break;
                case 'string':
                    if('' == trim($value)) {
                        break;
                    }
                    if( false !== strpos($value, '%') ) {
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

    protected function applyLimitAndOffset($limit = null, $offset = null)
    {
        if (null !== $limit) {
            $this->applyFilter('LIMIT', ['value' => $limit], true);
            if (null !== $offset) {
                $this->applyFilter('OFFSET', ['value' => $offset], true);
            }
        }
    }

    protected function bootstrapSql($type, array $fields = null)
    {
        if (!$fields) {
            $fields = ['*'];
        } else {
            array_unshift($fields, 'id');
            $fields = array_unique(array_map(function($field) {
                return $this->writer->esc(strtolower($field));
            }, $fields));
        }
        $table = $this->writer->esc($type);
        $fields = implode($fields, ', ');
        $this->sql = "SELECT {$fields} FROM {$table}";
    }

    protected function applyFilterOrFail($field, $arguments)
    {
        if ($this->isExpressModeOn()) { $this->AND; }
        list($token, $values) = $this->solveFilterArgs($arguments);
        $this->applyFilter($token, ['field' => $field, 'value' => $values]);
        $this->turnExpressModeOn();

        return $this;
    }

    protected function solveFilterArgs($args)
    {
        if (1 == count($args)) {
            return ['=', $args[0]];
        }

        return [$args[0], $args[1]];
    }

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

    protected function turnExpressModeOn()
    {
        $this->express = true;
    }

    protected function turnExpressModeOff()
    {
        $this->express = false;
    }

    protected function isExpressModeOn()
    {
        return $this->express;
    }

}
