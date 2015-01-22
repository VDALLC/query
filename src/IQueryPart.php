<?php
namespace Vda\Query;

interface IQueryPart
{
    public function onProcess(IQueryProcessor $processor);
}
