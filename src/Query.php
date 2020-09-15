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
        return \array_filter([
            'columns' => $builder->columns,
            'wheres' => \collect($builder->wheres)->map(static function ($where) {
                if (isset($where['query'])) {
                    $where['query'] = static::serialize($where['query']);
                }

                return $where;
            })->all(),
            'bindings' => $builder->bindings,
            'distinct' => $builder->distinct,
            'from' => $builder->from,
            'joins' => \collect($builder->joins)->map(static function ($join) {
                return JoinClause::serialize($join);
            })->all(),
            'groups' => $builder->groups,
            'orders' => $builder->orders,
        ]);
    }

    /**
     * Unserialize to basic Query Builder.
     */
    public static function unserialize(array $payload): QueryBuilder
    {
        $builder = DB::query();

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
