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
        $FilterResolver = new FilterResolver();
        $FilterResolver->getFilterInstanceOrFail($token)->apply($this->sql, $this->values, $field, $values);
        $this->turnExpressModeOff();

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
