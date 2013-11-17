<?php

namespace RedBeanPHP\Plugins\RedSql;

class FilterResolver
{
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

    public function getFilterInstanceOrFail($token)
    {
        $FilterClass = $this->solveFilterClass($token);
        if (!$this->filterExists($FilterClass)) {
            throw new \RuntimeException("\"{$token}\" is not a valid RedSql construct");
        }
        return (new $FilterClass());
    }

    public function filterExists($class)
    {
        if (class_exists($class)) {
           return true;
        }
        return false;
    }

    public function solveFilterClass($token)
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
}