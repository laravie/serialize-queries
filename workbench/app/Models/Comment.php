<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /** {@inheritDoc} */
    protected $table = 'comments';

    /** {@inheritDoc} */
    #[\Override]
    public static function boot()
    {
        parent::boot();

        static::booted();
    }

    /** {@inheritDoc} */
    #[\Override]
    public static function booted()
    {
        static::addGlobalScope('id', function (Builder $builder) {
            $builder->where('id', '<', 10);
        });
    }
}
