<?php

namespace Laravie\SerializesQuery;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;

class Serialize
{
    public static function execute($serializable): array
    {
        if ($serializable instanceof EloquentBuilder) {
            return Eloquent::serialize($serializable);
        } elseif ($serializable instanceof Relation) {
            return Related::serialize($serializable);
        }

        return Query::serialize($serializable);
    }
}
