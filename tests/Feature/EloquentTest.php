<?php

namespace Laravie\SerializesQuery\Tests\Feature;

use Illuminate\Database\Eloquent\Builder;
use Laravie\SerializesQuery\Tests\TestCase;
use Mockery as m;
use PHPUnit\Framework\Attributes\Test;
use Workbench\App\Models\Comment;
use Workbench\App\Models\Post;
use Workbench\App\Models\User;

use function Laravie\SerializesQuery\serialize;
use function Laravie\SerializesQuery\unserialize;

class EloquentTest extends TestCase
{
    #[Test]
    public function it_can_serialize_a_basic_eloquent_builder()
    {
        $builder = User::query();
        $serialized = serialize($builder);

        $this->assertSame([
            'model' => [
                'class' => User::class,
                'connection' => null,
                'eager' => [],
                'removedScopes' => [],
            ],
            'builder' => [
                'connection' => 'testing',
                'bindings' => $this->defaultBindings(),
                'from' => 'users',
            ],
        ], $serialized);

        $unserialize = unserialize($serialized);

        $this->assertSame('select * from "users" where "users"."deleted_at" is null', $unserialize->toSql());

        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }

    #[Test]
    public function it_can_serialize_a_basic_eloquent_builder_with_global_scopes()
    {
        $builder = Comment::query();
        $serialized = serialize($builder);

        $this->assertSame([
            'connection' => 'testing',
            'bindings' => $this->defaultBindings(),
            'from' => 'comments',
        ], $serialized['builder']);
        $this->assertSame(Comment::class, $serialized['model']['class']);

        $unserialize = unserialize($serialized);

        $this->assertSame('select * from "comments" where "id" < ?', $unserialize->toSql());
        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }

    #[Test]
    public function it_can_serialize_a_basic_eloquent_with_eager_relations()
    {
        $builder = User::with(['posts' => static fn ($query) => $query->where('id', '>', 10)]);

        $serialized = serialize($builder);

        $this->assertNotNull($serialized['model']['eager']['posts']);

        $unserialize = unserialize($serialized);

        $query = m::mock(Builder::class);

        $query->shouldReceive('where')->with('id', '>', 10)->andReturnSelf();

        $unserialize->getEagerLoads()['posts']($query);

        $this->assertSame('select * from "users" where "users"."deleted_at" is null', $unserialize->toSql());

        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }

    #[Test]
    public function it_can_serialize_a_softdeleted_eloquent_builder()
    {
        $builder = User::query()->onlyTrashed();
        $serialized = serialize($builder);

        $this->assertSame([
            'model' => [
                'class' => User::class,
                'connection' => null,
                'eager' => [],
                'removedScopes' => [
                    'Illuminate\Database\Eloquent\SoftDeletingScope',
                ],
            ],
            'builder' => [
                'connection' => 'testing',
                'bindings' => $this->defaultBindings(),
                'from' => 'users',
                'wheres' => [
                    ['type' => 'NotNull', 'column' => 'users.deleted_at', 'boolean' => 'and'],
                ],
            ],
        ], $serialized);

        $unserialize = unserialize($serialized);

        $this->assertSame('select * from "users" where "users"."deleted_at" is not null', $unserialize->toSql());

        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }

    #[Test]
    public function it_can_serialize_a_basic_eloquent_builder_on_custom_connection()
    {
        $builder = User::on('mysql');
        $serialized = serialize($builder);

        $this->assertSame([
            'model' => [
                'class' => User::class,
                'connection' => 'mysql',
                'eager' => [],
                'removedScopes' => [],
            ],
            'builder' => [
                'connection' => 'mysql',
                'bindings' => $this->defaultBindings(),
                'from' => 'users',
            ],
        ], $serialized);

        $unserialize = unserialize($serialized);

        $this->assertSame('select * from `users` where `users`.`deleted_at` is null', $unserialize->toSql());

        $this->assertSame($builder->toSql(), $unserialize->toSql());
        $this->assertSame('mysql', $unserialize->getModel()->getConnectionName());
    }

    #[Test]
    public function it_can_serialize_a_basic_eloquent_builder_with_wheres()
    {
        $builder = User::query()->where('email', '=', 'crynobone@gmail.com');
        $serialized = serialize($builder);

        $this->assertSame([
            'model' => [
                'class' => User::class,
                'connection' => null,
                'eager' => [],
                'removedScopes' => [],
            ],
            'builder' => [
                'connection' => 'testing',
                'bindings' => $this->defaultBindings(['where' => ['crynobone@gmail.com']]),
                'from' => 'users',
                'wheres' => [
                    ['type' => 'Basic', 'column' => 'email', 'operator' => '=', 'value' => 'crynobone@gmail.com', 'boolean' => 'and'],
                ],
            ],
        ], $serialized);

        $unserialize = unserialize($serialized);

        $this->assertSame('select * from "users" where "email" = ? and "users"."deleted_at" is null', $unserialize->toSql());

        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }

    #[Test]
    public function it_can_serialize_a_basic_eloquent_builder_with_join()
    {
        $builder = Post::whereHas('user', static fn ($query) => $query->where('users.email', '=', 'crynobone@gmail.com'));

        $serialized = serialize($builder);

        $this->assertSame([
            'model' => [
                'class' => Post::class,
                'connection' => null,
                'eager' => [],
                'removedScopes' => [],
            ],
            'builder' => [
                'connection' => 'testing',
                'bindings' => $this->defaultBindings(['where' => ['crynobone@gmail.com']]),
                'from' => 'posts',
                'wheres' => [
                    [
                        'type' => 'Exists',
                        'query' => [
                            'connection' => 'testing',
                            'columns' => ['*'],
                            'bindings' => $this->defaultBindings(['where' => ['crynobone@gmail.com']]),
                            'from' => 'users',
                            'wheres' => [
                                ['type' => 'Column', 'first' => 'posts.user_id', 'operator' => '=', 'second' => 'users.id', 'boolean' => 'and'],
                                ['type' => 'Basic', 'column' => 'users.email', 'operator' => '=', 'value' => 'crynobone@gmail.com', 'boolean' => 'and'],
                                ['type' => 'Null', 'column' => 'users.deleted_at', 'boolean' => 'and'],
                            ],
                        ],
                        'boolean' => 'and',
                    ],
                ],
            ],
        ], $serialized);

        $unserialize = unserialize($serialized);

        $this->assertSame('select * from "posts" where exists (select * from "users" where "posts"."user_id" = "users"."id" and "users"."email" = ? and "users"."deleted_at" is null)', $unserialize->toSql());

        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }

    #[Test]
    public function it_can_serialize_a_basic_eloquent_builder_with_belongs_to_many_join()
    {
        $builder = User::whereHas('roles', static fn ($query) => $query->whereIn('roles.id', [1]));

        $serialized = serialize($builder);

        $this->assertSame([
            'model' => [
                'class' => User::class,
                'connection' => null,
                'eager' => [],
                'removedScopes' => [],
            ],
            'builder' => [
                'connection' => 'testing',
                'bindings' => $this->defaultBindings(['where' => [1]]),
                'from' => 'users',
                'wheres' => [
                    [
                        'type' => 'Exists',
                        'query' => [
                            'connection' => 'testing',
                            'columns' => ['*'],
                            'bindings' => $this->defaultBindings(['where' => [1]]),
                            'from' => 'roles',
                            'joins' => [
                                [
                                    'connection' => 'testing',
                                    'bindings' => $this->defaultBindings(),
                                    'wheres' => [
                                        ['type' => 'Column', 'first' => 'roles.id', 'operator' => '=', 'second' => 'user_role.role_id', 'boolean' => 'and'],
                                    ],
                                    'type' => 'inner',
                                    'table' => 'user_role',
                                ],
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

        $unserialize = unserialize($serialized);

        $this->assertSame('select * from "users" where exists (select * from "roles" inner join "user_role" on "roles"."id" = "user_role"."role_id" where "users"."id" = "user_role"."user_id" and "roles"."id" in (?)) and "users"."deleted_at" is null', $unserialize->toSql());

        $this->assertSame($serialized, serialize($unserialize));

        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }

    #[Test]
    public function it_can_serialize_a_related_eloquent_builder()
    {
        $builder = (new User())->forceFill([
            'id' => 5,
        ])->posts();

        $serialized = serialize($builder);

        $this->assertSame([
            'model' => [
                'class' => Post::class,
                'connection' => null,
                'eager' => [],
                'removedScopes' => [],
            ],
            'builder' => [
                'connection' => 'testing',
                'bindings' => $this->defaultBindings([
                    'where' => [5],
                ]),
                'from' => 'posts',
                'wheres' => [
                    [
                        'type' => 'Basic',
                        'column' => 'posts.user_id',
                        'operator' => '=',
                        'value' => 5,
                        'boolean' => 'and',
                    ],
                    [
                        'type' => 'NotNull',
                        'column' => 'posts.user_id',
                        'boolean' => 'and',
                    ],
                ],
            ],
        ], $serialized);

        $unserialize = unserialize($serialized);

        $this->assertSame('select * from "posts" where "posts"."user_id" = ? and "posts"."user_id" is not null', $unserialize->toSql());

        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }
}
