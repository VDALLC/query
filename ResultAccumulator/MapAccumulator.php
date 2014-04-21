<?php
namespace Vda\Query\ResultAccumulator;

use Vda\Query\Projection\IProjection;

class MapAccumulator implements IResultAccumulator
{
    private $fieldNum;
    private $rowProjection;
    private $result;

    public function __construct($fieldNum)
    {
        $this->fieldNum = $fieldNum;
        $this->result = array();
    }

    public function reset(IProjection $projection = null)
    {
        $this->rowProjection = $projection;
        $this->result = array();
    }

    public function accumulate(array $tuple)
    {
        $this->result[$tuple[$this->fieldNum]] = $this->rowProjection->project($tuple);
    }

    public function isFilled()
    {
        return false;
    }

    public function getResult()
    {
        return $this->result;
    }
}
