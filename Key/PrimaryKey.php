<?php
namespace Vda\Query\Key;

class PrimaryKey
{
    private $fields;

    public function __construct($keyField)
    {
        $this->fields = func_get_args();
    }

    public function getFields()
    {
        return $this->fields;
    }
}
