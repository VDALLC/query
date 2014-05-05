<?php
namespace Vda\Query\Projection;

use Vda\Query\Alias;
use Vda\Query\Field;
use Vda\Util\BeanUtil;

class EntityProjection implements IProjection
{
    private $entityClass;
    private $fieldIndices;

    public function __construct($entityClass, $fields)
    {
        $this->entityClass = $entityClass;
        $this->fieldIndices = array();

        $entityFields = BeanUtil::listProperties($entityClass);
        foreach ($fields as $i => $field) {
            if ($field instanceof Field) {
                $name = $field->getPropertyName();
            } elseif ($field instanceof Alias) {
                $name = $field->getAlias();
            } else {
                continue;
            }

            if (in_array($name, $entityFields)) {
                $this->fieldIndices[$i] = $name;
            }
        }
    }

    public function project(array $tuple)
    {
        $result = new $this->entityClass;

        foreach ($this->fieldIndices as $idx => $name) {
            $method = 'set' . $name;
            if (method_exists($result, $method)) {
                $result->{$method}($tuple[$idx]);
            } else {
                $result->$name = $tuple[$idx];
            }
        }

        return $result;
    }
}
