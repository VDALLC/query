<?php
namespace Vda\Query\ResultAccumulator;

use Vda\Query\Projection\IProjection;

interface IResultAccumulator
{
    /**
     * Reset currently accumulated values
     */
    public function reset(IProjection $rowProjection);

    /**
     * Generate index for given row
     *
     * @param array $tuple
     */
    public function accumulate(array $tuple);

    /**
     * @return boolean
     */
    public function isFilled();

    /**
     * @return mixed
     */
    public function getResult();
}
