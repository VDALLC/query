<?php
namespace Vda\Query;

use Vda\Query\Operator\Operator;
use Vda\Util\BeanUtil;
use Vda\Util\Type;

class Insert implements IQueryPart
{
    /**
     * @var Table
     */
    private $table;

    /**
     * @var Field[]
     */
    private $fields = array();
    private $values = array();
    private $valuesIndex = 0;
    private $select;

    public static function insert()
    {
        return new self();
    }

    public function into(Table $table)
    {
        $this->table = $table;

        return $this;
    }

    public function fields()
    {
        $this->fields = func_get_args();

        return $this;
    }

    public function values()
    {
        $this->values[$this->valuesIndex] = array();

        foreach (func_get_args() as $i => $value) {
            if (isset($this->fields[$i])) {
                $type = $this->fields[$i]->getType();
            } else {
                $type = Type::AUTO;
            }

            $this->values[$this->valuesIndex][$i] = $this->normalize($value, $type);
        }

        return $this;
    }

    public function set(Field $field, $value)
    {
        if ($this->valuesIndex == 0) {
            $this->fields[] = $field;
        }
        $this->values[$this->valuesIndex][] = $this->normalize($value, $field->getType());

        return $this;
    }

    public function populate($bean)
    {
        $map = BeanUtil::toArray($bean);

        $fields = $this->fields ? : $this->table->getFields();

        foreach ($fields as $f) {
            $propName = $f->getPropertyName();
            if (array_key_exists($propName, $map)) {
                $this->set($f, $map[$propName]);
            }
        }

        return $this;
    }

    public function select(Select $select)
    {
        $this->select = $select;

        return $this;
    }

    public function hasFields()
    {
        return !empty($this->fields);
    }

    public function isFromSelect()
    {
        return !empty($this->select);
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getSelect()
    {
        return $this->select;
    }

    public function batch()
    {
        $this->valuesIndex++;

        return $this;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        $processor->processInsertQuery($this);
    }

    private function normalize($value, $type)
    {
        if (!$value instanceof IExpression) {
            return Operator::constant($value, $type);
        }

        return $value;
    }
}
