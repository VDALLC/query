<?php
namespace Vda\Query\Operator;

use Vda\Query\IExpression;
use Vda\Query\IQueryProcessor;

final class BinaryOperator extends AbstractOperator
{
    private $operand1;
    private $operand2;

    public function __construct($mnemonic, $operand1, $operand2)
    {
        parent::__construct($mnemonic);

        $this->operand1 = $this->normalizeOperand($operand1);
        $this->operand2 = $this->normalizeOperand($operand2);
    }

    public function getOperand1()
    {
        return $this->operand1;
    }

    public function getOperand2()
    {
        return $this->operand2;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        return $processor->processBinaryOperator($this);
    }
}
