<?php

namespace Laravie\SerializesQuery\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    /**
     * Has many to relationship with Post.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Has many and belongs to relationship with Role.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id')->withTimestamps();
    }
}
