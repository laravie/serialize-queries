<?php

namespace Laravie\SerializesQuery;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

class Unserialize
{
    public function __invoke($payload)
    {
        if ($payload instanceof Relation) {
            return $payload->unserialize();
        }

        if (Arr::has($payload, 'model')) {
            return Eloquent::unserialize($payload);
        }

        return Query::unserialize($payload);
    }
}
