<?php

namespace Vda\Query\Projection;


/**
 * @template T
 */
class CallbackProjection implements IProjection
{
    /**
     * @param \Closure(array<int, mixed>): T $callback Projection function
     */
    public function __construct(
        private \Closure $callback
    ) {}

    /**
     * @return T
     */
    public function project($tuple)
    {
        return ($this->callback)($tuple);
    }
}
