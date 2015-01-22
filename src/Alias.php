<?php
namespace Vda\Query;

class Alias implements IExpression
{
    private $expression;
    private $alias;

    public function __construct(IExpression $expression, $alias)
    {
        $this->expression = $expression;
        $this->alias = $alias;
    }

    public function getExpression()
    {
        return $this->expression;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        return $processor->processAlias($this);
    }
}
