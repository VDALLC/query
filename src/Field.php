<?php
namespace Vda\Query;

class Field implements IExpression
{
    use OperatorsTrait;

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
