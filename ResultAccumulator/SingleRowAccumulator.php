<?php
namespace Vda\Query\ResultAccumulator;

use Vda\Query\Projection\IProjection;

class SingleRowAccumulator implements IResultAccumulator
{
    private $result;
    private $isFilled;
    private $rowProjection;

    public function reset(IProjection $rowProjection)
    {
        $this->result = null;
        $this->isFilled = false;
        $this->rowProjection = $rowProjection;
    }

    public function accumulate(array $tuple)
    {
        if (!$this->isFilled) {
            $this->isFilled = true;
            $this->result = $this->rowProjection->project($tuple);
        }
    }

    public function isFilled()
    {
        return $this->isFilled;
    }

    public function getResult()
    {
        return $this->result;
    }
}
