<?php
namespace Vda\Query\Operator;

use Vda\Query\IExpression;
use Vda\Query\IQueryProcessor;

final class CompositeOperator extends AbstractOperator
{
    private $operands;

    public function __construct($mnemonic, array $operands = array())
    {
        parent::__construct($mnemonic);

        $this->resetOperands($operands);
    }

    public function addOperand(IExpression $operator)
    {
        $this->operands[] = $operator;
    }

    public function getOperands()
    {
        return $this->operands;
    }

    public function resetOperands(array $operands)
    {
        $this->operands = array_map(array($this, 'normalizeOperand'), $operands);
    }

    public function onProcess(IQueryProcessor $processor)
    {
        return $processor->processCompositeOperator($this);
    }

    public function __toString()
    {
        return '(' . join(" {$this->getMnemonic()} ", $this->operands) . ')';
    }
}
