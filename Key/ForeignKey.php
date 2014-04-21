<?php
namespace Vda\Query\Key;

use Vda\Query\Table;

abstract class ForeignKey
{
    private $targetClass;

    public function __construct($targetClass, Table $table = null)
    {
        $this->targetClass = ltrim($targetClass, '\\');
        $this->table = $table;
    }

    public function init(Table $table)
    {
        $this->table = $table;
    }

    public function getTargetClass()
    {
        return $this->targetClass;
    }

    public function getTable()
    {
        return $this->table;
    }
}
