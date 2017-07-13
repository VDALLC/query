<?php
namespace Vda\Query;

use Vda\Query\Operator\Operator;
use Vda\Util\BeanUtil;

class Update implements IQueryPart
{
    /**
     * @var Table[]
     */
    private $tables;

    private $fields;
    private $expressions;
    private $criteria;

    public function __construct(Table $table)
    {
        $this->tables = array($table);
        $this->fields = array();
        $this->expressions = array();
    }

    public static function update(Table $table)
    {
        return new self($table);
    }

    public function set(Field $field, $value)
    {
        $this->fields[] = $field;

        if (!$value instanceof IExpression) {
            $value = Operator::constant($value, $field->getType());
        }

        $this->expressions[] = $value;

        return $this;
    }

    public function populate($bean)
    {
        $map = BeanUtil::toArray($bean);

        foreach ($this->tables[0]->getFields() as $f) {
            $propName = $f->getPropertyName();
            if (array_key_exists($propName, $map)) {
                $this->set($f, $map[$propName]);
            }
        }

        return $this;
    }

    public function where(IExpression ...$criteria)
    {
        if (count($criteria) == 1) {
            $this->criteria = $criteria[0];
        } else {
            $this->criteria = Operator::andOp(...$criteria);
        }

        return $this;
    }

    /**
     * @return Table[]
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return IExpression[]
     */
    public function getExpressions()
    {
        return $this->expressions;
    }

    /**
     * @return IExpression
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        return $processor->processUpdateQuery($this);
    }
}
