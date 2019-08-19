<?php
namespace Vda\Query\Operator;

use Vda\Query\IExpression;
use Vda\Query\IQueryProcessor;
use Vda\Query\OperatorsTrait;

class JsonGet implements IExpression
{
    use OperatorsTrait;

    private $doc;
    private $path;

    public function __construct(IExpression $doc, string $path)
    {
        $this->doc = $doc;
        $this->path = $path;
    }

    public function getDoc(): IExpression
    {
        return $this->doc;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        return $processor->processJsonGet($this);
    }
}
