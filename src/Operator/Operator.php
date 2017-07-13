<?php
namespace Vda\Query\Operator;

use Vda\Query\IExpression;
use Vda\Util\Type;

final class Operator
{
    const MNEMONIC_CONST              = 'const';
    const MNEMONIC_CALL               = '()';
    const MNEMONIC_UNARY_NOT          = '!';
    const MNEMONIC_UNARY_ISNULL       = 'is_null';
    const MNEMONIC_UNARY_NOTNULL      = 'not_null';
    const MNEMONIC_BINARY_MINUS       = '-';
    const MNEMONIC_BINARY_DIVIDE      = '/';
    const MNEMONIC_BINARY_EQ          = '==';
    const MNEMONIC_BINARY_GT          = '>';
    const MNEMONIC_BINARY_GTE         = '>=';
    const MNEMONIC_BINARY_LT          = '<';
    const MNEMONIC_BINARY_LTE         = '<=';
    const MNEMONIC_BINARY_INSET       = 'IN';
    const MNEMONIC_BINARY_MATCH       = '~';
    const MNEMONIC_BINARY_MATCHI      = '~*';
    const MNEMONIC_BINARY_NEQ         = '!=';
    const MNEMONIC_BINARY_NOTINSET    = '!IN';
    const MNEMONIC_BINARY_NOTMATCH    = '!~';
    const MNEMONIC_BINARY_NOTMATCHI   = '!~*';
    const MNEMONIC_COMPOSITE_AND      = '&&';
    const MNEMONIC_COMPOSITE_OR       = '||';
    const MNEMONIC_COMPOSITE_PLUS     = '+';
    const MNEMONIC_COMPOSITE_MULTIPLY = '*';

    private static $null;
    private static $false;
    private static $true;

    private function __construct()
    {
    }

    /**
     * @return CompositeOperator
     */
    public static function andOp()
    {
        return new CompositeOperator(self::MNEMONIC_COMPOSITE_AND, func_get_args());
    }

    public static function orOp()
    {
        return new CompositeOperator(self::MNEMONIC_COMPOSITE_OR, func_get_args());
    }

    public static function plus()
    {
        return new CompositeOperator(self::MNEMONIC_COMPOSITE_PLUS, func_get_args());
    }

    public static function mul()
    {
        return new CompositeOperator(self::MNEMONIC_COMPOSITE_MULTIPLY, func_get_args());
    }

    public static function count($arg = null)
    {
        $arg = is_null($arg) ? array() : array($arg);

        return self::call(FunctionCall::FUNCTION_COUNT, $arg);
    }

    /**
     * Call aggregate function sum().
     *
     * @see self::plus()
     *
     * @param $arg
     * @return FunctionCall
     */
    public static function sum($arg)
    {
        return self::call(FunctionCall::FUNCTION_SUM, array($arg));
    }

    public static function avg($arg)
    {
        return self::call(FunctionCall::FUNCTION_AVG, array($arg));
    }

    public static function min($arg)
    {
        return self::call(FunctionCall::FUNCTION_MIN, array($arg));
    }

    public static function max($arg)
    {
        return self::call(FunctionCall::FUNCTION_MAX, array($arg));
    }

    public static function call($funcName, array $args = array())
    {
        return new FunctionCall(self::MNEMONIC_CALL, $funcName, $args);
    }

    public static function minus($operand1, $operand2)
    {
        return new BinaryOperator(self::MNEMONIC_BINARY_MINUS, $operand1, $operand2);
    }

    public static function div($operand1, $operand2)
    {
        return new BinaryOperator(self::MNEMONIC_BINARY_DIVIDE, $operand1, $operand2);
    }

    public static function eq($operand1, $operand2)
    {
        return new BinaryOperator(self::MNEMONIC_BINARY_EQ, $operand1, $operand2);
    }

    public static function neq($operand1, $operand2)
    {
        return new BinaryOperator(self::MNEMONIC_BINARY_NEQ, $operand1, $operand2);
    }

    public static function gt($operand1, $operand2)
    {
        return new BinaryOperator(self::MNEMONIC_BINARY_GT, $operand1, $operand2);
    }

    public static function gte($operand1, $operand2)
    {
        return new BinaryOperator(self::MNEMONIC_BINARY_GTE, $operand1, $operand2);
    }

    public static function lt($operand1, $operand2)
    {
        return new BinaryOperator(self::MNEMONIC_BINARY_LT, $operand1, $operand2);
    }

    public static function lte($operand1, $operand2)
    {
        return new BinaryOperator(self::MNEMONIC_BINARY_LTE, $operand1, $operand2);
    }

    public static function in($operand1, $operand2)
    {
        return new BinaryOperator(self::MNEMONIC_BINARY_INSET, $operand1, $operand2);
    }

    public static function notin($operand1, $operand2)
    {
        return new BinaryOperator(self::MNEMONIC_BINARY_NOTINSET, $operand1, $operand2);
    }

    /**
     * Case sensitive pattern match
     *
     * Valid whitelabel characters are '?' (matches any single character) and
     * '*' (matches any sequence of characters). The escape character is '\'.
     *
     * <code>
     * $pattern = '\\foo\\?bar\\*baz';
     * </code>
     *
     * Such a pattern will match exactly \foo?bar*baz string.
     *
     * <code>
     * $pattern = '\\foo?bar*baz';
     * </code>
     *
     * This pattern will match any string that matches following criterion:
     *
     * \foo<any char>bar<any char sequence>baz
     *
     * @param mixed $operand1 Value to test against the pattern
     * @param mixed $operand2 Pattern
     * @return mixed Return value depends on expression processor
     */
    public static function match($operand1, $operand2)
    {
        if (is_scalar($operand2)) {
            $operand2 = new Mask(self::MNEMONIC_CONST, $operand2);
        }

        return new BinaryOperator(self::MNEMONIC_BINARY_MATCH, $operand1, $operand2);
    }

    /**
     * Case insensitive pattern match
     *
     * @see Operator::match() For pattern format
     */
    public static function matchi($operand1, $operand2)
    {
        if (is_scalar($operand2)) {
            $operand2 = new Mask(self::MNEMONIC_CONST, $operand2);
        }

        return new BinaryOperator(self::MNEMONIC_BINARY_MATCHI, $operand1, $operand2);
    }

    /**
     * Negated case sensitive pattern match
     *
     * @see Operator::match() For pattern format
     */
    public static function notmatch($operand1, $operand2)
    {
        if (is_scalar($operand2)) {
            $operand2 = new Mask(self::MNEMONIC_CONST, $operand2);
        }

        return new BinaryOperator(self::MNEMONIC_BINARY_NOTMATCH, $operand1, $operand2);
    }

    /**
     * Negated case insensitive pattern match
     *
     * @see Operator::match() For pattern format
     */
    public static function notmatchi($operand1, $operand2)
    {
        if (is_scalar($operand2)) {
            $operand2 = new Mask(self::MNEMONIC_CONST, $operand2);
        }

        return new BinaryOperator(self::MNEMONIC_BINARY_NOTMATCHI, $operand1, $operand2);
    }

    /**
     * Negates it's operand
     *
     * @param IExpression $operand Expression to negate
     * @return mixed Return value depends on expression processor
     */
    public static function not(IExpression $operand)
    {
        return new UnaryOperator(self::MNEMONIC_UNARY_NOT, $operand);
    }

    public static function isnull(IExpression $operand)
    {
        return new UnaryOperator(self::MNEMONIC_UNARY_ISNULL, $operand);
    }

    public static function notnull(IExpression $operand)
    {
        return new UnaryOperator(self::MNEMONIC_UNARY_NOTNULL, $operand);
    }

    public static function nullConst()
    {
        if (empty(self::$null)) {
            self::$null = self::createConstant(null, Type::DYNAMIC);
        }

        return self::$null;
    }

    public static function falseConst()
    {
        if (empty(self::$false)) {
            self::$false = self::createConstant(false, Type::BOOLEAN);
        }

        return self::$false;
    }

    public static function trueConst()
    {
        if (empty(self::$true)) {
            self::$true = self::createConstant(true, Type::BOOLEAN);
        }

        return self::$true;
    }

    public static function constant($value, $type = Type::AUTO)
    {
        if (is_null($value)) {
            return self::nullConst();
        }

        if ($type === Type::BOOLEAN) {
            return empty($value) ? self::falseConst() : self::trueConst();
        }

        if ($type === Type::AUTO) {
            $type = is_array($value) ? $type | Type::COLLECTION : Type::resolveType($value);
        }

        return self::createConstant($value, $type);
    }

    private static function createConstant($value, $type)
    {
        return new Constant(self::MNEMONIC_CONST, $value, $type);
    }
}
