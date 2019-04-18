<?php
namespace Vda\Query;

class Alias implements IExpression
{
    private $expression;
    private $alias;

    public function __construct(IExpression $expression, string $alias)
    {
        $this->expression = $expression;
        $this->alias = $alias;
    }

    public function getExpression(): IExpression
    {
        return $this->expression;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        return $processor->processAlias($this);
    }
}
