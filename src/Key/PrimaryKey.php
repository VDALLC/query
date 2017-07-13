<?php
namespace Vda\Query\Key;

class PrimaryKey
{
    private $fields;

    public function __construct(...$keyFields)
    {
        $this->fields = $keyFields;
    }

    public function getFields()
    {
        return $this->fields;
    }
}
