<?php
namespace Vda\Query\Projection;

interface IProjection
{
    /**
     * Convert tuple (indexed array) to any desired type
     *
     * @param array $tuple
     * @return mixed
     */
    public function project(array $tuple);
}
