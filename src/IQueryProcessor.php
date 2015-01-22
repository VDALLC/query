<?php
namespace Vda\Query;

use Vda\Query\Operator\BinaryOperator;
use Vda\Query\Operator\CompositeOperator;
use Vda\Query\Operator\Constant;
use Vda\Query\Operator\FunctionCall;
use Vda\Query\Operator\Mask;
use Vda\Query\Operator\UnaryOperator;

interface IQueryProcessor
{
    public function processSelectQuery(Select $select);
    public function processInsertQuery(Insert $insert);
    public function processUpdateQuery(Update $update);
    public function processUpsertQuery(Upsert $upsert);
    public function processDeleteQuery(Delete $delete);
    public function processField(Field $field);
    public function processTable(Table $table);
    public function processJoin(JoinClause $join);
    public function processUnaryOperator(UnaryOperator $operator);
    public function processBinaryOperator(BinaryOperator $operator);
    public function processCompositeOperator(CompositeOperator $operator);
    public function processConstant(Constant $const);
    public function processMask(Mask $mask);
    public function processFunctionCall(FunctionCall $call);
    public function processOrder(Order $order);
    public function processAlias(Alias $alias);
}
