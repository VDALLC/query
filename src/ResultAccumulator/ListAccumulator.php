<?php
namespace Vda\Query\ResultAccumulator;

use Vda\Query\Projection\IProjection;

class ListAccumulator implements IResultAccumulator
{
    private $rowProjection;
    private $result;

    public function __construct()
    {
        $this->result = [];
    }

    public function reset(IProjection $projection)
    {
        $this->rowProjection = $projection;
        $this->result = [];
    }

    public function accumulate(array $tuple)
    {
        $this->result[] = $this->rowProjection->project($tuple);
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
