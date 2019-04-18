<?php
namespace Vda\Query;

use Vda\Query\Operator\Operator;

class Upsert implements IQueryPart
{
    /**
     * @var Table
     */
    private $table;

    /**
     * @var IExpression
     */
    private $criteria;

    /**
     * @var Field[]
     */
    private $insertFields = [];
    private $insertValues = [];
    /**
     * @var Field[]
     */
    private $updateFields = [];
    private $updateValues = [];

    public static function upsert()
    {
        return new self();
    }

    /**
     * Set merge target table
     *
     * @param Table $table
     * @return Upsert
     */
    public function into(Table $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Set the criteria to detect duplicate entries
     *
     * @param IExpression $criteria
     * @return Upsert
     */
    public function check(IExpression $criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * Add one field-value pair to insert clause
     *
     * @param Field $f
     * @param mixed $value
     * @return Upsert
     */
    public function insert(Field $field, $value)
    {
        if (!$value instanceof IExpression) {
            $value = Operator::constant($value, $field->getType());
        }

        $this->insertFields[] = $field;
        $this->insertValues[] = $value;

        return $this;
    }

    /**
     * Add one field-value pair to update clause
     *
     * @param Field $f
     * @param mixed $value
     * @return Upsert
     */
    public function update(Field $field, $value)
    {
        if (!$value instanceof IExpression) {
            $value = Operator::constant($value, $field->getType());
        }

        $this->updateFields[] = $field;
        $this->updateValues[] = $value;

        return $this;
    }

    /**
     * Get upsert target table
     *
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    public function getCriteria()
    {
        return $this->criteria;
    }

    public function getInsertFields()
    {
        return $this->insertFields;
    }

    public function getInsertValues()
    {
        return $this->insertValues;
    }

    public function getUpdateFields()
    {
        return $this->updateFields;
    }

    public function getUpdateValues()
    {
        return $this->updateValues;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        return $processor->processUpsertQuery($this);
    }
}
