<?php
namespace Vda\Query\Operator;

use Vda\Query\IQueryProcessor;
use Vda\Util\Type;

class Constant extends AbstractOperator
{
    private $value;
    private $type;

    public function __construct($mnemonic, $value, $type)
    {
        parent::__construct($mnemonic);

        $this->value = $value;
        $this->type = $type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        return $processor->processConstant($this);
    }

    public function __toString()
    {
        return self::toString($this->value, $this->type);
    }

    public static function toString($value, $type = Type::STRING)
    {
        if ($value === null) {
            return 'null';
        }

        $isArray = ($type & Type::COLLECTION) > 0;

        switch ($type & ~Type::COLLECTION) {
            case Type::BOOLEAN:
                $render = 'self::renderBoolean';
                break;
            case Type::STRING:
                $render = 'self::renderString';
                break;
            default:
                $render = 'self::renderDefault';
        }

        if ($isArray) {
            return '[' . \join(', ', $render($value)) . ']';
        } else {
            return $render($value);
        }
    }

    private static function renderBoolean($value)
    {
        return $value ? 'true' : 'false';
    }

    private static function renderString($value)
    {
        return "'" . \addslashes($value) . "'";
    }

    private static function renderDefault($value)
    {
        return (string) $value;
    }
}
