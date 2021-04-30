<?php

namespace Laravie\SerializesQuery;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as FluentQueryBuilder;

/**
 * Serialize query builder.
 *
 * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Query\Builder  $builder
 */
function serialize($builder): array
{
    if ($builder instanceof FluentQueryBuilder) {
        return Query::serialize($builder);
    }

    return Eloquent::serialize($builder);
}

/**
 * Unserialize query builder.
 *
 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
 */
function unserialize(array $payload)
{
    if (! (isset($payload['model']) && isset($payload['builder']))) {
        return Query::unserialize($payload);
    }

    return Eloquent::unserialize($payload);
}
