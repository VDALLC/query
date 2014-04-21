<?php
namespace Vda\Query\Projection;

class SingleColumnProjection implements IProjection
{
    private $fieldNum;

    public function __construct($fieldNum)
    {
        $this->fieldNum = $fieldNum;
    }

    public function project(array $tuple)
    {
        return $tuple[$this->fieldNum];
    }
}
