<?php
namespace Vda\Query;

class Order implements IQueryPart
{
    const DIR_ASC  = 'ASC';
    const DIR_DESC = 'DESC';

    private $property;
    private $direction;

    public function __construct(IExpression $property, $direction = self::DIR_ASC)
    {
        $this->property = $property;
        $this->direction = $direction;
    }

    /**
     * @return IExpression
     */
    public function getProperty()
    {
        return $this->property;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function isAsc()
    {
        return $this->direction == self::DIR_ASC;
    }

    public function isDesc()
    {
        return $this->direction == self::DIR_DESC;
    }

    public function onProcess(IQueryProcessor $processor)
    {
        return $processor->processOrder($this);
    }
}
