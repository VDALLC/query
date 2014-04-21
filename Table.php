<?php
namespace Vda\Query;

use Vda\Query\Key\ForeignKey;

abstract class Table implements IFieldList, IQueryPart, ISource
{
    public $_primaryKey;

    private $_name;
    private $_alias;

    /**
     * @var Field[]
     */
    private $_fields;
    private $_foreignKeys;

    protected function __construct($name, $alias, $forceInit = true)
    {
        $this->_name = $name;
        $this->_alias = $alias;

        if ($forceInit) {
            $this->loadFields(true);
        }
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        if (is_null($this->_fields)) {
            $this->loadFields(false);
        }

        return $this->_fields;
    }

    public function getField($name)
    {
        return $this->{$name};
    }

    public function getForeignKeys()
    {
        if (is_null($this->_foreignKeys)) {
            $this->loadFields(false);
        }

        return $this->_foreignKeys;
    }

    public function getForeignKey($refTableClass)
    {
        if (is_null($this->_foreignKeys)) {
            $this->loadFields(false);
        }

        return isset($this->_foreignKeys[$refTableClass])
            ? $this->_foreignKeys[$refTableClass]
            : null;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getAlias()
    {
        return $this->_alias;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        $processor->processTable($this);
    }

    private function loadFields($init)
    {
        $rc = new \ReflectionClass($this);

        foreach ($rc->getProperties() as $p) {
            if (!$p->isPublic() || $p->isStatic()) {
                continue;
            }

            $name = $p->getName();
            $field = $p->getValue($this);

            if ($field instanceof ForeignKey) {
                $this->registerForeignKey($field, $init);
            } elseif ($field instanceof Field) {
                $this->registerField($field, $name, $init);
            }
        }
    }

    private function registerField(Field $field, $name, $init)
    {
        if ($init) {
            $field->init($name, $this);
        }

        $this->_fields[] = $field;
    }

    private function registerForeignKey(ForeignKey $key, $init)
    {
        if ($init) {
            $key->init($this);
        }

        $this->_foreignKeys[$key->getTargetClass()] = $key;
    }
}
