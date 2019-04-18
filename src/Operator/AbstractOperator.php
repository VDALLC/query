<?php
namespace Vda\Query\Operator;

use Vda\Query\Alias;
use Vda\Query\IExpression;
use Vda\Query\IQueryProcessor;

abstract class AbstractOperator implements IExpression
{
    private $mnemonic;

    protected function __construct($mnemonic)
    {
        $this->mnemonic = $mnemonic;
    }

    public abstract function onProcess(IQueryProcessor $processor);

    public function getMnemonic()
    {
        return $this->mnemonic;
    }

    public function as(string $alias)
    {
        return new Alias($this, $alias);
    }

    /**
     * @deprecated Use self::as() instead
     */
    public function _as($alias)
    {
        return $this->as($alias);
    }

    protected function normalizeOperand($operand)
    {
        if (!$operand instanceof IExpression) {
            return Operator::constant($operand);
        }

        return $operand;
    }
}
