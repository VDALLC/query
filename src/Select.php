<?php
namespace Vda\Query;

use Vda\Query\Key\ManyToMany;
use Vda\Query\Key\OneToN;
use Vda\Query\Operator\Operator;
use Vda\Query\Projection\AssocProjection;
use Vda\Query\Projection\EntityProjection;
use Vda\Query\Projection\IProjection;
use Vda\Query\Projection\SingleColumnProjection;
use Vda\Query\ResultAccumulator\IResultAccumulator;
use Vda\Query\ResultAccumulator\ListAccumulator;
use Vda\Query\ResultAccumulator\MapAccumulator;
use Vda\Query\ResultAccumulator\SingleRowAccumulator;

/**
 * Class Select
 *
 * @method SourceAlias as($alias)
 *
 * @package Vda\Query
 */
class Select implements IExpression, IFieldList
{
    const LOCK_NONE         = 0;
    const LOCK_FOR_UPDATE   = 1;
    const LOCK_FOR_SHARE    = 2;

    private static $rc;

    /**
     * @var ISource[]|JoinClause[]
     */
    private $sources;

    /**
     * @var ISource[]
     */
    private $reversedSources;

    /**
     * @var IExpression
     */
    private $fields;

    /**
     * @var IExpression
     */
    private $criteria;

    private $detectFields;

    private $groups;

    private $orders;

    private $limit;

    private $offset;

    private $resultAccumulator;

    private $projection;

    /**
     * Select lock mode.
     *
     * @see http://www.postgresql.org/docs/9.0/static/sql-select.html
     * @see http://www.jooq.org/doc/3.0/manual/sql-building/sql-statements/select-statement/for-update-clause/
     * @var int
     */
    private $lock = self::LOCK_NONE;

    /**
     * @param IExpression...
     */
    public function __construct(...$args)
    {
        $this->sources = [];
        $this->fields  = [];

        foreach ($args as $arg) {
            if ($arg instanceof Table) {
                $this->fields = \array_merge($this->fields, $arg->getFields());
            } elseif ($arg instanceof IExpression) {
                $this->fields[] = $arg;
            } else {
                throw new \InvalidArgumentException(
                    'Select expression must be an instance of ' . IExpression::class
                );
            }
        }

        $this->detectFields = empty($this->fields);
    }

    /**
     * @param IExpression...
     * @return self
     */
    public static function select(...$args)
    {
        return new self(...$args);
    }

    /**
     * @param ISource... $exp
     * @return self
     */
    public function from(ISource ...$source)
    {
        foreach ($source as $src) {
            $this->sources[] = $src;

            if ($this->detectFields) {
                $this->fields = \array_merge($this->fields, $src->getFields());
            }
        }

        $this->reversedSources = \array_reverse($this->sources);

        return $this;
    }

    /**
     * @param ISource $target
     * @param IExpression|OneToN|ManyToMany|bool $on
     * @return self
     */
    public function join(ISource $target, $on = false)
    {
        return $this->_join(JoinClause::TYPE_INNER, $target, $on);
    }

    /**
     * @param ISource $target
     * @param IExpression|OneToN|ManyToMany|false $on
     * @return self
     */
    public function leftJoin(ISource $target, $on = false)
    {
        return $this->_join(JoinClause::TYPE_LEFT, $target, $on);
    }

    /**
     * @param IExpression ...$criteria|null
     * @return self
     */
    public function where(...$criteria)
    {
        if (\count($criteria) == 1) {
            $this->criteria = $criteria[0];
        } else {
            $this->criteria = Operator::andOp(...$criteria);
        }

        return $this;
    }

    public function groupBy(...$field)
    {
        $this->groups = \is_array($field[0]) ? $field[0] : $field;

        return $this;
    }

    public function orderBy(...$order)
    {
        $this->orders = \is_array($order[0]) ? $order[0] : $order;

        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function accumulate(IResultAccumulator $accumulator)
    {
        $this->resultAccumulator = $accumulator;

        return $this;
    }

    public function transform(IProjection $projection)
    {
        $this->projection = $projection;

        return $this;
    }

    public function singleRow()
    {
        $this->limit(1);
        $this->accumulate(new SingleRowAccumulator());

        return $this;
    }

    public function indexBy($fieldNum)
    {
        $this->accumulate(new MapAccumulator($fieldNum));

        return $this;
    }

    public function map($entityClass)
    {
        $this->transform(new EntityProjection($entityClass, $this->getFields()));

        return $this;
    }

    public function singleColumn($fieldNum = 0)
    {
        $this->transform(new SingleColumnProjection($fieldNum));

        return $this;
    }

    /**
     * @param int $fieldNum
     * @return $this
     */
    public function singleValue($fieldNum = 0)
    {
        return $this->singleRow()->singleColumn($fieldNum);
    }

    public function forUpdate()
    {
        $this->lock = self::LOCK_FOR_UPDATE;

        return $this;
    }

    public function forShare()
    {
        $this->lock = self::LOCK_FOR_SHARE;

        return $this;
    }

    public function as(string $alias)
    {
        return new SourceAlias($this, $alias);
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
        return $processor->processSelectQuery($this);
    }

    public function getSources()
    {
        return $this->sources;
    }

    public function getCriteria()
    {
        return $this->criteria;
    }

    public function getField($name)
    {
        foreach ($this->fields as $field) {
            if ($field instanceof Field && $field->getName() == $name) {
                return $field;
            } elseif ($field instanceof Alias && $field->getAlias() == $name) {
                return $field;
            }
        }

        return null;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getResultAccumulator()
    {
        if (empty($this->resultAccumulator)) {
            $this->resultAccumulator = new ListAccumulator();
        }

        return $this->resultAccumulator;
    }

    public function getProjection()
    {
        if (empty($this->projection)) {
            $this->projection = new AssocProjection($this->getFields());
        }

        return $this->projection;
    }

    public function filter(Filter $filter)
    {
        $this->criteria = $filter->getCriterion();
        $this->orders = $filter->getOrder() ?: null;
        $this->limit = $filter->getLimit();
        $this->offset = $filter->getOffset();

        return $this;
    }

    public function getLockMode()
    {
        return $this->lock;
    }

    private function _join($type, ISource $right, $on)
    {
        //FIXME Do fields autodetection
        if ($on === false) {
            $this->addJoinClause($type, $right, null);
        } elseif ($on instanceof ManyToMany) {
            $jt = $on->getJoinTable('_jt_' . $right->getAlias());

            if ($on->getTable() === $right) {
                foreach ($this->reversedSources as $s) {
                    $fkLeft = $jt->getForeignKey(\get_class($s));
                    if (!empty($fkLeft)) {
                        break;
                    }
                }
            } else {
                $fkLeft = $jt->getForeignKey(\get_class($on->getTable()));
            }

            $this->_join($type, $jt, $fkLeft);
            $this->_join($type, $right, $jt->getForeignKey(\get_class($right)));

        } elseif ($on instanceof OneToN) {
            $this->addJoinClause($type, $right, $this->buildOneToNJoinCriterion($right, $on));
        } elseif ($on instanceof IExpression) {
            $this->addJoinClause($type, $right, $on);
        } else {
            throw new \InvalidArgumentException(
                "Join criterion should be either instance of IExpression or ForeignKey or false"
            );
        }

        return $this;
    }

    private function findLeftJoinTarget($targetClass)
    {
        foreach ($this->reversedSources as $s) {
            if (\get_class($s) == $targetClass) {
                return $s;
            }
        }

        throw new \InvalidArgumentException(
            "Invalid foreign key. Unable to find suitable join target by class name"
        );
    }

    private function checkLeftJoinTarget($table)
    {
        foreach ($this->reversedSources as $s) {
            if ($s === $table) {
                return true;
            }
        }

        throw new \InvalidArgumentException(
            "Invalid foreign key. Key owning table is not present in source list"
        );
    }

    private function buildOneToNJoinCriterion(ISource $right, OneToN $on)
    {
        if ($on->getTable() === $right) {
            $left = $this->findLeftJoinTarget($on->getTargetClass());
            $jc = \array_flip($on->getJoinColumns());
        } elseif (\get_class($right) == $on->getTargetClass()) {
            $left = $on->getTable();
            $this->checkLeftJoinTarget($left);
            $jc = $on->getJoinColumns();
        } else {
            throw new \InvalidArgumentException(
                "Invalid foreign key. Join target's class not matches one specified in key"
            );
        }

        $criterion = Operator::andOp();

        foreach ($jc as $l => $r) {
            $criterion->addOperand(
                $left->getField($l)->eq($right->getField($r))
            );
        }

        return $criterion;
    }

    private function addJoinClause($type, $target, $criterion)
    {
        \array_unshift($this->reversedSources, $target);

        $this->sources[] = new JoinClause($type, $target, $criterion);

        if ($this->detectFields) {
            $this->fields = \array_merge($this->fields, $target->getFields());
        }
    }
}
