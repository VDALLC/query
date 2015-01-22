<?php
namespace Vda\Query;

interface IFieldList
{
    /**
     * @return IExpression[]
     */
    public function getFields();

    /**
     * @param string $name
     * @return Field
     */
    public function getField($name);
}
