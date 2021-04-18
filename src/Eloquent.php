<?php

namespace Laravie\SerializesQuery;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Queue\SerializableClosure;

class Eloquent
{
    /**
     * Serialize from Eloquent Query Builder.
     */
    public static function serialize($builder): array
    {
        if ($builder instanceof Relation) {
            $builder = $builder->getQuery();
        }

        $model = $builder->getModel();

        return [
            'model' => [
                'class' => \get_class($model),
                'connection' => $model->getConnectionName(),
                'eager' => \collect($builder->getEagerLoads())->map(function ($callback) {
                    return \serialize(new SerializableClosure($callback));
                })->all(),
                'removedScopes' => $builder->removedScopes(),
            ],
            'builder' => Query::serialize($builder->getQuery()),
        ];
    }

    /**
     * Unserialize to Eloquent Query Builder.
     */
    public static function unserialize(array $payload): EloquentBuilder
    {
        $model = \tap(new $payload['model']['class'](), static function ($model) use ($payload) {
            $model->setConnection($payload['model']['connection']);
        });

        $builder = (new EloquentBuilder(
            Query::unserialize($payload['builder'])
        ))->setModel($model);

        return $model->registerGlobalScopes($builder)
            ->setEagerLoads(
                \collect($payload['model']['eager'])->map(function ($callback) {
                    return \unserialize($callback)->getClosure();
                })->all()
            )->withoutGlobalScopes($payload['model']['removedScopes']);
    }
}
