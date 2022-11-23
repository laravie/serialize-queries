<?php

namespace Laravie\SerializesQuery;

use Illuminate\Database\Query\Builder as FluentQueryBuilder;
use Illuminate\Database\Query\JoinClause as JoinClauseBuilder;
use Illuminate\Support\Arr;

class JoinClause
{
    /**
     * Serialize to Join Clause Query Builder.
     *
     * @return array<string, mixed>
     */
    public static function serialize(JoinClauseBuilder $builder): array
    {
        return array_merge(Query::serialize($builder), [
            'type' => $builder->type,
            'table' => $builder->table,
        ]);
    }

    /**
     * Unserialize to Join Clause Query Builder.
     *
     * @param  array<int, array<string, mixed>>  $joins
     */
    public static function unserialize(FluentQueryBuilder $builder, array $joins): array
    {
        $results = [];

        foreach ($joins as $join) {
            $type = $join['type'];
            $table = $join['table'];

            $joinClauseBuilder = new JoinClauseBuilder(
                $builder, $type, $table
            );

            Query::unserializeFor($joinClauseBuilder, Arr::except($join, ['type', 'table']));

            $results[] = $joinClauseBuilder;
        }

        return $results;
    }
}
