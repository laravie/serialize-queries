<?php

namespace Laravie\SerializesQuery\Tests\Feature;

use Laravie\SerializesQuery\Eloquent;
use Laravie\SerializesQuery\Tests\Models\Post;
use Laravie\SerializesQuery\Tests\Models\User;
use Laravie\SerializesQuery\Tests\TestCase;

class EloquentTest extends TestCase
{
    /** @test */
    public function it_can_serialize_a_basic_eloquent_builder()
    {
        $builder = User::query();
        $serialized = Eloquent::serialize($builder);

        $this->assertSame([
            'model' => [
                'class' => User::class,
                'eager' => [],
            ],
            'builder' => [
                'bindings' => [
                    'select' => [], 'from' => [], 'join' => [], 'where' => [], 'groupBy' => [], 'having' => [], 'order' => [], 'union' => [], 'unionOrder' => [],
                ],
                'from' => 'users',
            ],
        ], $serialized);

        $unserialize = Eloquent::unserialize($serialized);

        $this->assertSame('select * from `users`', $unserialize->toSql());

        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }

    /** @test */
    public function it_can_serialize_a_basic_eloquent_builder_with_wheres()
    {
        $builder = User::query()->where('email', '=', 'crynobone@gmail.com');
        $serialized = Eloquent::serialize($builder);

        $this->assertSame([
            'model' => [
                'class' => User::class,
                'eager' => [],
            ],
            'builder' => [
                'bindings' => [
                    'select' => [], 'from' => [], 'join' => [], 'where' => ['crynobone@gmail.com'], 'groupBy' => [], 'having' => [], 'order' => [], 'union' => [], 'unionOrder' => [],
                ],
                'from' => 'users',
                'wheres' => [
                    ['type' => 'Basic', 'column' => 'email', 'operator' => '=', 'value' => 'crynobone@gmail.com', 'boolean' => 'and'],
                ],
            ],
        ], $serialized);

        $unserialize = Eloquent::unserialize($serialized);

        $this->assertSame('select * from `users` where `email` = ?', $unserialize->toSql());

        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }

    /** @test */
    public function it_can_serialize_a_basic_eloquent_builder_with_join()
    {
        $builder = Post::whereHas('user', function ($query) {
            return $query->where('users.email', '=', 'crynobone@gmail.com');
        });

        $serialized = Eloquent::serialize($builder);

        $this->assertSame([
            'model' => [
                'class' => Post::class,
                'eager' => [],
            ],
            'builder' => [
                'bindings' => [
                    'select' => [], 'from' => [], 'join' => [], 'where' => ['crynobone@gmail.com'], 'groupBy' => [], 'having' => [], 'order' => [], 'union' => [], 'unionOrder' => [],
                ],
                'from' => 'posts',
                'wheres' => [
                    [
                        'type' => 'Exists',
                        'query' => [
                            'columns' => ['*'],
                            'bindings' => ['select' => [], 'from' => [], 'join' => [], 'where' => ['crynobone@gmail.com'], 'groupBy' => [], 'having' => [], 'order' => [], 'union' => [], 'unionOrder' => []],
                            'from' => 'users',
                            'wheres' => [
                                ['type' => 'Column', 'first' => 'posts.user_id', 'operator' => '=', 'second' => 'users.id', 'boolean' => 'and'],
                                ['type' => 'Basic', 'column' => 'users.email', 'operator' => '=', 'value' => 'crynobone@gmail.com', 'boolean' => 'and'],
                            ],
                        ],
                        'boolean' => 'and',
                    ],
                ],
            ],
        ], $serialized);

        $unserialize = Eloquent::unserialize($serialized);

        $this->assertSame('select * from `posts` where exists (select * from `users` where `posts`.`user_id` = `users`.`id` and `users`.`email` = ?)', $unserialize->toSql());

        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }


    /** @test */
    public function it_can_serialize_a_basic_eloquent_builder_with_belongs_to_many_join()
    {
        $builder = User::whereHas('roles', function ($query) {
            return $query->whereIn('roles.id', [1]);
        });

        $serialized = Eloquent::serialize($builder);

        $this->assertSame([
            'model' => [
                'class' => User::class,
                'eager' => [],
            ],
            'builder' => [
                'bindings' => [
                    'select' => [], 'from' => [], 'join' => [], 'where' => [1], 'groupBy' => [], 'having' => [], 'order' => [], 'union' => [], 'unionOrder' => [],
                ],
                'from' => 'users',
                'wheres' => [
                    [
                        'type' => 'Exists',
                        'query' => [
                            'columns' => ['*'],
                            'bindings' => ['select' => [], 'from' => [], 'join' => [], 'where' => [1], 'groupBy' => [], 'having' => [], 'order' => [], 'union' => [], 'unionOrder' => []],
                            'from' => 'roles',
                            'joins' => [
                                [
                                    'bindings' => [
                                        'select' => [], 'from' => [], 'join' => [], 'where' => [], 'groupBy' => [], 'having' => [], 'order' => [], 'union' => [], 'unionOrder' => [],
                                    ],
                                    'wheres' => [
                                        ['type' => 'Column', 'first' => 'roles.id', 'operator' => '=', 'second' => 'user_role.role_id', 'boolean' => 'and'],
                                    ],
                                    'type' => 'inner',
                                    'table' => 'user_role',
                                ]
                            ],
                            'wheres' => [
                                ['type' => 'Column', 'first' => 'users.id', 'operator' => '=', 'second' => 'user_role.user_id', 'boolean' => 'and'],
                                ['type' => 'In', 'column' => 'roles.id', 'values' => [1], 'boolean' => 'and'],
                            ],
                        ],
                        'boolean' => 'and',
                    ],
                ],
            ],
        ], $serialized);

        $unserialize = Eloquent::unserialize($serialized);

        $this->assertSame('select * from `users` where exists (select * from `roles` inner join `user_role` on `roles`.`id` = `user_role`.`role_id` where `users`.`id` = `user_role`.`user_id` and `roles`.`id` in (?))', $unserialize->toSql());

        $this->assertSame($serialized, Eloquent::serialize($unserialize));

        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }
}
