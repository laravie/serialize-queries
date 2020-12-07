<?php

namespace Laravie\SerializesQuery;

class Serialize
{
    public function __invoke($instance)
    {
        return $instance->serialize();
    }
}
