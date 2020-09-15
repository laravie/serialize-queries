<?php

namespace Laravie\SerializesQuery;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Queue\SerializableClosure;

class Eloquent
{
    /**
     * Serialize from Eloquent Query Builder.
     */
    public static function serialize(EloquentBuilder $builder): array
    {
        return [
            'model' => [
                'class' => \get_class($builder->getModel()),
                'eager' => \collect($builder->getEagerLoads())->map(function ($callback) {
                    return \serialize(new SerializableClosure($callback));
                })->all(),
            ],
            'builder' => Query::serialize($builder->getQuery()),
        ];
    }

    /**
     * Unserialize to Eloquent Query Builder.
     */
    public static function unserialize(array $payload): EloquentBuilder
    {
        return (new EloquentBuilder(Query::unserialize($payload['builder'])))
            ->setModel(new $payload['model']['class']())
            ->setEagerLoads(
                collect($payload['model']['eager'])->map(function ($callback) {
                    return unserialize($callback);
                })->all()
            );
    }
}
