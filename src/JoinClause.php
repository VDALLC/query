<?php
namespace Vda\Query;

class JoinClause implements IQueryPart
{
    const TYPE_INNER = 0;
    const TYPE_LEFT  = 1;

    private $type;
    private $target;
    private $criterion;

    public function __construct($type, ISource $target, IExpression $criterion = null)
    {
        $this->type = $type;
        $this->target = $target;
        $this->criterion = $criterion;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @return ISource
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return IExpression
     */
    public function getCriterion()
    {
        return $this->criterion;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        $processor->processJoin($this);
    }
}
