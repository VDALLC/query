<?php
namespace Vda\Query\Projection;

use Vda\Query\Table;

class ClassNameFieldProjection implements IProjection
{
    protected $classNameField;

    /**
     * @var Table
     */
    protected $dao;

    public function __construct($classNameField, Table $dao)
    {
        $this->classNameField = $classNameField;
        $this->dao = $dao;
    }

    public function project(array $tuple)
    {
        if (class_exists($tuple[$this->classNameField])) {
            $class = $tuple[$this->classNameField];
        } else {
            $class = $this->dao->_entityClass;
        }

        $result = new $class;

        foreach ($this->dao->getFields() as $idx => $field) {
            $result->{'set' . $field->getName()}($tuple[$idx]);
        }

        return $result;
    }
}
