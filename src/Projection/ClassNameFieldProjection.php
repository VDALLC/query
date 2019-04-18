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

    protected $fieldIndex;

    public function __construct($classNameField, Table $dao)
    {
        $this->classNameField = $classNameField;
        foreach ($dao->getFields() as $index => $field) {
            if ($field->getName() == $classNameField) {
                $this->fieldIndex = $index;
            }
        }
        $this->dao = $dao;
    }

    public function project(array $tuple)
    {
        if (\class_exists($tuple[$this->fieldIndex])) {
            $class = $tuple[$this->fieldIndex];
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
