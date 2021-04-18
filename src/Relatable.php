<?php

namespace Laravie\SerializesQuery;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Relatable
{
    /**
     * Serialize from Eloquent Relation.
     */
    public static function serialize(Relation $relation): array
    {
        return Eloquent::serialize($relation->getQuery());
    }

    /**
     * Unserialize to Eloquent Query Builder.
     */
    public static function unserialize(array $payload): EloquentBuilder
    {
        return Eloquent::unserialize($payload);
    }
}
