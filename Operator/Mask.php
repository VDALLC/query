<?php
namespace Vda\Query\Operator;

use Vda\Util\Type;
use Vda\Query\IQueryProcessor;

class Mask extends AbstractOperator
{
    private $mask;

    public function __construct($mnemonic, $mask)
    {
        parent::__construct($mnemonic);

        $this->mask = $mask;
    }

    public function getMask()
    {
        return $this->mask;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        return $processor->processMask($this);
    }

    public function __toString()
    {
        return "'{$this->mask}'";
    }
}
