<?php
namespace Vda\Query;

use Vda\Query\Operator\Operator;

class Filter
{
    protected $criterion;
    protected $order = [];
    protected $limit;
    protected $offset;

    public function andWhere(IExpression ...$criteria)
    {
        if (!empty($this->criterion)) {
            \array_unshift($criteria, $this->criterion);
        }

        $this->criterion = $this->argsToCriteria($criteria);
    }

    /**
     * Add number of anded criteria
     *
     * @param IExpression $ex, ...
     */
    public function where(IExpression ...$criteria)
    {
        $this->criterion = $this->argsToCriteria($criteria);

        return $this;
    }

    /**
     * @return IExpression
     */
    public function getCriterion()
    {
        return $this->criterion;
    }

    /**
     * @param Order $order,...
     */
    public function orderBy(Order ...$order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return Order[]
     */
    public function getOrder()
    {
        return $this->order;
    }

    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    private function argsToCriteria($args)
    {
        if (\count($args) == 1) {
            return $args[0];
        }

        return Operator::andOp(...$args);
    }
}
