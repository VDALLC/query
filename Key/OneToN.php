<?php
namespace Vda\Query\Key;

use Vda\Query\Table;

abstract class OneToN extends ForeignKey
{
    private $joinColumns;

    public function __construct($targetClass, array $joinColumns, Table $table = null)
    {
        parent::__construct($targetClass, $table);

        $this->joinColumns = $joinColumns;
    }

    public function getJoinColumns()
    {
        return $this->joinColumns;
    }
}
