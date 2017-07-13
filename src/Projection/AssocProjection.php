<?php
namespace Vda\Query\Projection;

use Vda\Query\Alias;
use Vda\Query\Field;

class AssocProjection implements IProjection
{
    private $fieldIndicies;

    public function __construct($fields)
    {
        $this->fieldIndicies = array();

        foreach ($fields as $i => $field) {
            if ($field instanceof Field) {
                $name = $field->getPropertyName();
            } elseif ($field instanceof Alias) {
                $name = $field->getAlias();
            } else {
                $name = $i;
            }

            $this->fieldIndicies[$i] = $name;
        }
    }

    public function project(array $tuple)
    {
        $result = array();

        foreach ($this->fieldIndicies as $idx => $name) {
            $result[$name] = $tuple[$idx];
        }

        return $result;
    }
}
