<?php
namespace Vda\Query\Operator;

use Vda\Query\IQueryProcessor;

class FunctionCall extends AbstractOperator
{
    private $funcName;
    private $args;

    public function __construct($mnemonic, $funcName, array $args)
    {
        parent::__construct($mnemonic);

        $this->funcName = $funcName;
        $this->args = array_map(array($this, 'normalizeOperand'), $args);
    }

    public function getName()
    {
        return $this->funcName;
    }

    public function getArgs()
    {
        return $this->args;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        return $processor->processFunctionCall($this);
    }

    public function __toString()
    {
        return "{$this->funcName}(" . join(', ', $this->args) . ')';
    }
}
