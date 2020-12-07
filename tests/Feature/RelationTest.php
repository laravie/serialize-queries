<?php

namespace Laravie\SerializesQuery\Tests\Feature;

use Laravie\SerializesQuery\Tests\Models\Post;
use Laravie\SerializesQuery\Tests\Models\User;
use Laravie\SerializesQuery\Tests\TestCase;

class RelationTest extends TestCase
{
    /** @test */
    public function it_can_serialize_a_basic_relation()
    {
        $user = tap(new User(), fn ($u) => $u->id = 1);
        $builder = $user->posts();

        $serialized = $builder->serialize();

        $unserialize = $builder->unserialize();

        $this->assertSame($builder->toSql(), $unserialize->toSql());
        $this->assertSame($builder->getBindings(), $unserialize->getBindings());
    }
}
