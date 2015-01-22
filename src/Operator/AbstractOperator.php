<?php
namespace Vda\Query\Operator;

use Vda\Query\Alias;
use Vda\Query\IQueryProcessor;
use Vda\Query\IExpression;

/**
 * @method Alias as($alias)
 */
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

    public function _as($alias)
    {
        return new Alias($this, $alias);
    }

    //TODO Get rid of this once PHP is fixed to allow keywords as method names
    public function __call($method, $args)
    {
        if ($method === 'as') {
            return $this->_as($args[0]);
        }

        trigger_error('Call to undefined method ' . __CLASS__ . '::' . $method, E_USER_ERROR);
    }

    protected function normalizeOperand($operand)
    {
        if (!$operand instanceof IExpression) {
            return Operator::constant($operand);
        }

        return $operand;
    }
}
