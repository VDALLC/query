<?php
namespace Vda\Query;

use Vda\Query\Operator\CompositeOperator;
use Vda\Query\Operator\Operator;

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

    /**
     * @return enum Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * @return ISource
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return Order
     */
    public function asc()
    {
        return new Order($this, Order::DIR_ASC);
    }

    /**
     * @return Order
     */
    public function desc()
    {
        return new Order($this, Order::DIR_DESC);
    }

    /**
     * @param mixed $exp Either Field instance or scalar value
     * @return CompositeOperator
     */
    public function eq($exp)
    {
        return Operator::eq($this, $exp);
    }

    public function gt($exp)
    {
        return Operator::gt($this, $exp);
    }

    public function gte($exp)
    {
        return Operator::gte($this, $exp);
    }

    public function lt($exp)
    {
        return Operator::lt($this, $exp);
    }

    public function lte($exp)
    {
        return Operator::lte($this, $exp);
    }

    public function like($exp)
    {
        return Operator::match($this, $exp);
    }

    public function ilike($exp)
    {
        return Operator::matchi($this, $exp);
    }

    public function notlike($exp)
    {
        return Operator::notmatch($this, $exp);
    }

    public function notilike($exp)
    {
        return Operator::notmatchi($this, $exp);
    }

    public function in($exp)
    {
        if (!is_array($exp) && !($exp instanceof Select && func_num_args() == 1)) {
            $exp = func_get_args();
        }

        return Operator::in($this, $exp);
    }

    public function notin($exp)
    {
        if (!is_array($exp) && !($exp instanceof Select && func_num_args() == 1)) {
            $exp = func_get_args();
        }

        return Operator::notin($this, $exp);
    }

    public function neq($exp)
    {
        return Operator::neq($this, $exp);
    }

    public function isnull()
    {
        return Operator::isnull($this);
    }

    public function notnull()
    {
        return Operator::notnull($this);
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

    public function onProcess(IQueryProcessor $processor)
    {
        $processor->processField($this);
    }
}
