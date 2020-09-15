<?php

namespace Laravie\SerializesQuery;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class Query
{
    /**
     * Serialize to basic Query Builder.
     */
    public static function serialize(QueryBuilder $builder): array
    {
        $connection = $builder->getConnection();

        return \array_filter([
            'connection' => \is_string($connection) ? $connection : $connection->getName(),
            'columns' => $builder->columns,
            'bindings' => $builder->bindings,
            'distinct' => $builder->distinct,
            'from' => $builder->from,
            'joins' => \collect($builder->joins)->map(static function ($join) {
                return JoinClause::serialize($join);
            })->all(),
            'wheres' => \collect($builder->wheres)->map(static function ($where) {
                if (isset($where['query'])) {
                    $where['query'] = static::serialize($where['query']);
                }

                return $where;
            })->all(),
            'groups' => $builder->groups,
            'havings' => $builder->havings,
            'orders' => $builder->orders,
            'limit' => $builder->limit,
            'offset' => $builder->offset,
            'unions' => $builder->unions,
            'unionLimit' => $builder->unionLimit,
            'unionOrders' => $builder->unionOrders,
            'lock' => $builder->lock,
        ]);
    }

    /**
     * Unserialize to basic Query Builder.
     */
    public static function unserialize(array $payload): QueryBuilder
    {
        $connection = $payload['connection'] ?? null;

        unset($payload['connection']);

        return static::unserializeFor(DB::connection($connection)->query(), $payload);
    }

    /**
     * Unserialize for basic Query Builder.
     */
    public static function unserializeFor(QueryBuilder $builder, array $payload): QueryBuilder
    {
        \collect($payload)->transform(static function ($value, $type) use ($builder) {
            if ($type === 'wheres') {
                foreach ($value as $index => $where) {
                    if (isset($where['query']) && \is_array($where['query'])) {
                        $value[$index]['query'] = static::unserialize($where['query']);
                    }
                }
            }

            if ($type === 'joins') {
                $value = JoinClause::unserialize($builder, $value ?? []);
            }

            return $value;
        })->each(static function ($value, $type) use ($builder) {
            $builder->{$type} = $value;
        });

        return $builder;
    }
}
