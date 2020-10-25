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
        $model = $builder->getModel();

        return [
            'model' => [
                'class' => \get_class($model),
                'connection' => $model->getConnectionName(),
                'eager' => \collect($builder->getEagerLoads())->map(function ($callback) {
                    return \serialize(new SerializableClosure($callback));
                })->all(),
                'globalScopes' => \collect($model->getGlobalScopes())->map(function ($callback) {
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
        $modelName = $payload['model']['class'];

        $model = \tap(new $modelName(), static function ($model) use ($payload) {
            $model->setConnection($payload['model']['connection']);
        });

        $builder = (new EloquentBuilder(Query::unserialize($payload['builder'])))
            ->setModel($model)
            ->withoutGlobalScopes($payload['model']['removedScopes'])
            ->setEagerLoads(
                \collect($payload['model']['eager'])->map(function ($callback) {
                    return \unserialize($callback)->getClosure();
                })->all()
            );

        \collect($payload['model']['globalScopes'])->map(function ($callback, $name) use ($builder) {
            $builder->withGlobalScope($name, \unserialize($callback)->getClosure());
        });

        return $builder;
    }
}
