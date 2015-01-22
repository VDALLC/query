<?php
namespace Vda\Query;

interface ISource extends IFieldList
{
    public function getAlias();
    public function getName();
}
