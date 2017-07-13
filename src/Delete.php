<?php
namespace Vda\Query;

use Vda\Query\Operator\Operator;

class Delete implements IQueryPart
{
    private $tables;
    private $criteria;

    public static function delete()
    {
        return new self();
    }

    public function from(Table $table)
    {
        $this->tables = array($table);

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
     * @return IExpression
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        return $processor->processDeleteQuery($this);
    }
}
