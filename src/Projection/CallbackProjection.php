<?php

namespace Vda\Query\Projection;

/**
 * @template T
 */
class CallbackProjection implements IProjection
{
    /**
     * @param callable(array<int, mixed>): T $callback Projection function
     */
    public function __construct(
        private callable $callaback
    ) {
    }

    /**
     * @return T
     */
    public function project($tuple)
    {
        return ($this->callaback)($tuple);
    }
}
