<?php

namespace Laravie\SerializesQuery;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\ServiceProvider;
use Laravie\SerializesQuery\Eloquent;
use Laravie\SerializesQuery\Query;
use Laravie\SerializesQuery\Relation;

class SerializesQueryProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        EloquentRelation::macro('serialize', function () {
            $this->query = $this->query->serialize();

            return $this;
        });

        EloquentRelation::macro('unserialize', function () {
            $this->query = Eloquent::unserialize($this->query);

            return $this;
        });

        EloquentBuilder::macro('serialize', function () {
            return Eloquent::serialize($this);
        });

        QueryBuilder::macro('serialize', function () {
            return Query::serialize($this);
        });

        EloquentBuilder::macro('serialize', function () {
            return Eloquent::serialize($this);
        });

        QueryBuilder::macro('serialize', function () {
            return Query::serialize($this);
        });
    }
}
