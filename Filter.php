<?php
namespace Vda\Query;

use Vda\Query\Operator\Operator;

class Filter
{
    protected $criterion;
    protected $order = array();
    protected $limit;
    protected $offset;

    public function andWhere(IExpression $criterion)
    {
        if (empty($this->criterion)) {
            $this->criterion = $this->argsToCriterion(func_get_args());
        } else {
            $this->criterion = Operator::andOp(
                $this->criterion,
                $this->argsToCriterion(func_get_args())
            );
        }
    }

    /**
     * Add number of anded criteria
     *
     * @param Expression $ex, ...
     */
    public function where(IExpression $criterion)
    {
        $this->criterion = $this->argsToCriterion(func_get_args());

        return $this;
    }

    private function argsToCriterion(array $args)
    {
        if (count($args) == 0) {
            return null;
        } elseif (count($args) == 1) {
            return $args[0];
        } else {
            $res = Operator::andOp();
            foreach ($args as $op) {
                $res->addOperand($op);
            }
            return $res;
        }
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
    public function orderBy(Order $order)
    {
        $this->order = func_get_args();

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
}
