<?php
namespace Vda\Query\Operator;

use Vda\Query\IExpression;
use Vda\Query\IQueryProcessor;

final class UnaryOperator extends AbstractOperator
{
    private $operand;

    public function __construct($mnemonic, IExpression $operand)
    {
        parent::__construct($mnemonic);

        $this->operand = $operand;
    }

    /**
     * @return IExpression
     */
    public function getOperand()
    {
        return $this->operand;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        return $processor->processUnaryOperator($this);
    }

    public function __toString()
    {
        return "{$this->getMnemonic()}({$this->operand})";
    }
}
