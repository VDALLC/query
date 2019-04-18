<?php
namespace Vda\Query;

use Vda\Query\Operator\BinaryOperator;
use Vda\Query\Operator\Operator;
use Vda\Query\Operator\UnaryOperator;

/**
 * @method Alias as(string $alias) Create an alias for this field
 */
class Field implements IExpression
{
    private $type;
    private $name;
    private $propertyName;
    private $scope;

    public function __construct($type, $name = null, $propertyName = null, ISource $scope = null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->propertyName = $propertyName;
        $this->scope = $scope;
    }

    public function init($fieldName, ISource $scope)
    {
        if ($this->propertyName === null) {
            $this->propertyName = $fieldName;
        }

        if ($this->name === null) {
            $this->name = $fieldName;
        }

        $this->scope = $scope;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getScope(): ?ISource
    {
        return $this->scope;
    }

    public function asc(): Order
    {
        return new Order($this, Order::DIR_ASC);
    }

    public function desc(): Order
    {
        return new Order($this, Order::DIR_DESC);
    }

    public function eq($exp): BinaryOperator
    {
        return Operator::eq($this, $exp);
    }

    public function gt($exp): BinaryOperator
    {
        return Operator::gt($this, $exp);
    }

    public function gte($exp): BinaryOperator
    {
        return Operator::gte($this, $exp);
    }

    public function lt($exp): BinaryOperator
    {
        return Operator::lt($this, $exp);
    }

    public function lte($exp): BinaryOperator
    {
        return Operator::lte($this, $exp);
    }

    public function like($exp): BinaryOperator
    {
        return Operator::match($this, $exp);
    }

    public function ilike($exp): BinaryOperator
    {
        return Operator::matchi($this, $exp);
    }

    public function notlike($exp): BinaryOperator
    {
        return Operator::notmatch($this, $exp);
    }

    public function notilike($exp): BinaryOperator
    {
        return Operator::notmatchi($this, $exp);
    }

    public function in(...$exp): BinaryOperator
    {
        if (\is_array($exp[0]) || ($exp[0] instanceof Select && \count($exp) == 1)) {
            $exp = $exp[0];
        }

        return Operator::in($this, $exp);
    }

    public function notin(...$exp): BinaryOperator
    {
        if (\is_array($exp[0]) || ($exp[0] instanceof Select && \count($exp) == 1)) {
            $exp = $exp[0];
        }

        return Operator::notin($this, $exp);
    }

    public function neq($exp): BinaryOperator
    {
        return Operator::neq($this, $exp);
    }

    public function isnull(): UnaryOperator
    {
        return Operator::isnull($this);
    }

    public function notnull(): UnaryOperator
    {
        return Operator::notnull($this);
    }

    public function as(string $alias): Alias
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

    public function onProcess(IQueryProcessor $processor)
    {
        return $processor->processField($this);
    }
}
