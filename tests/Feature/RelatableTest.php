<?php

namespace Laravie\SerializesQuery\Tests\Feature;

use Laravie\SerializesQuery\Relatable;
use Laravie\SerializesQuery\Tests\Models\Post;
use Laravie\SerializesQuery\Tests\Models\User;
use Laravie\SerializesQuery\Tests\TestCase;

class RelatableTest extends TestCase
{
    /** @test */
    public function it_can_serialize_a_related_eloquent_builder()
    {
        $builder = (new User())->forceFill([
            'id' => 5,
        ])->posts();
        $serialized = Relatable::serialize($builder);

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
                    ]
                ],
            ],
        ], $serialized);

        $unserialize = Relatable::unserialize($serialized);

        $this->assertSame('select * from "posts" where "posts"."user_id" = ? and "posts"."user_id" is not null', $unserialize->toSql());

        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }
}
