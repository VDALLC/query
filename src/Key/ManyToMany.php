<?php
namespace Vda\Query\Key;

use Vda\Query\Table;

class ManyToMany extends ForeignKey
{
    private $joinTableClass;

    public function __construct($targetClass, $joinTableClass, Table $table = null)
    {
        parent::__construct($targetClass, $table);

        $this->joinTableClass = \ltrim($joinTableClass, '\\');
    }

    /**
     * @param string $alias
     * @return \Vda\Query\Table
     */
    public function getJoinTable($alias)
    {
        return new $this->joinTableClass($alias);
    }
}
