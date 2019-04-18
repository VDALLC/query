<?php
namespace Vda\Query;

use Vda\Util\Type;

class SourceAlias extends Alias implements ISource
{
    public function __construct(Select $select, $alias)
    {
        parent::__construct($select, $alias);
    }

    public function getFields()
    {
        return \array_map([$this, 'convertExpression'], $this->getExpression()->getFields());
    }

    public function getField($name)
    {
        return $this->convertExpression($this->getExpression()->getField($name));
    }

    public function getName()
    {
        return $this->getAlias();
    }

    private function convertExpression($f)
    {
        if ($f instanceof Alias) {
            return new Field(Type::DYNAMIC, $f->getAlias(), $f->getAlias(), $this);
        } elseif ($f instanceof Field) {
            return new Field($f->getType(), $f->getName(), $f->getPropertyName(), $this);
        }

        //TODO throw an UnexpectedValueException here?
        return new Field(Type::DYNAMIC, null, null, $this);
    }
}
