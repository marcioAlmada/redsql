<?php

namespace RedBeanPHP\Plugins\RedSql;

use R;

class Finder
{
    /**
     * Bean type
     * @var string
     */
    protected $type;

    /**
     * SQL where string
     * @var string
     */
    protected $sql = '';

    /**
     * SQL values
     * @var array
     */
    protected $values = [];

    /**
     * This map helps Finder to resolve filters for arithmetic operators
     * @var array
     */
    protected $map = [
        '='  => 'EQUALS',
        '!=' => 'NOTEQUALS',
        '<>' => 'NOTEQUALS',
        '<'  => 'LESS',
        '>'  => 'GREATER',
        '>=' => 'GREATEROREQUALS',
        '<=' => 'LESSOREQUALS'
    ];

    /**
     * Allows express syntax, flagging finder to trigger AND operator automatically
     * @var boolean
     */
    protected $express = false;

    public function __construct($type)
    {
        R::dispense($type);
        $this->type = $type;
        $this->turnExpressModeOff();
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
        return $this->applyFilter($token);
    }

    protected function createConditionOrFail($field, $arguments)
    {
        if ($this->isExpressModeOn()) { $this->AND; }
        list($token, $values) = $this->solveFilterArgs($arguments);
        $this->applyFilter($token, $field, $values);
        $this->turnExpressModeOn();

        return $this;
    }

    protected function solveFilterArgs($args)
    {
        if (1 === count($args)) {
            return ['=', $args[0]];
        }

        return [$args[0], $args[1]];
    }

    protected function applyFilter($token, $field = null, $values = null)
    {
        $FilterClass = $this->solveFilterClass($token);
        if (!$this->filterExists($FilterClass)) {
            throw new \RuntimeException("\"{$token}\" is not a valid RedSql construct");
        }
        (new $FilterClass())->apply($this->sql, $this->values, $field, $values);
        $this->turnExpressModeOff();

        return $this;
    }

    protected function filterExists($class)
    {
        if (class_exists($class)) {
           return true;
        }

        return false;
    }

    protected function solveFilterClass($token)
    {
        $real_token = $this->sanitizeToken($token);
        if (in_array($real_token, array_keys($this->map))) {
            $real_token = $this->map[$token];
        }

        return __NAMESPACE__.'\Filters\Filter'.$real_token;
    }

    protected function sanitizeToken($token)
    {
        return strtoupper(preg_replace('/\s+/', '', $token));
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
