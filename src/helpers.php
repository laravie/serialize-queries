<?php

namespace Laravie\SerializesQuery;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as FluentQueryBuilder;
use InvalidArgumentException;

/**
 * Serialize query builder.
 *
 * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Query\Builder|mixed  $builder
 * @return array<string, mixed>
 */
function serialize($builder): array
{
    if ($builder instanceof FluentQueryBuilder) {
        return Query::serialize($builder);
    }

    if ($builder instanceof EloquentQueryBuilder || $builder instanceof Relation) {
        return Eloquent::serialize($builder);
    }

    throw new InvalidArgumentException('Unable to serialize $builder.');
}

/**
 * Unserialize query builder.
 *
 * @param  string|array<string, mixed>  $serialized
 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
 */
function unserialize($serialized)
{
    $payload = \is_string($serialized) ? unserialize($serialized) : $serialized;

    if (\is_array($payload)) {
        if (isset($payload['model']) && isset($payload['builder'])) {
            return Eloquent::unserialize($payload);
        }

        return Query::unserialize($payload);
    }

    throw new InvalidArgumentException('Unable to unserialize $payload.');
}
