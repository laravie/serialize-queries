<?php

namespace Laravie\SerializesQuery\Tests\Feature;

use Laravie\SerializesQuery\Serialize;
use Laravie\SerializesQuery\Unserialize;
use Laravie\SerializesQuery\Tests\Models\Post;
use Laravie\SerializesQuery\Tests\Models\User;
use Laravie\SerializesQuery\Tests\TestCase;

class SerializationTest extends TestCase
{
    /** @test */
    public function it_can_serialize_relations()
    {
        $user = tap(new User(), fn ($u) => $u->id = 1);
        $builder = $user->posts();

        $serialized = (new Serialize())($builder);
        $unserialize = (new Unserialize())($serialized);

        $this->assertSame($builder->toSql(), $unserialize->toSql());
        $this->assertSame($builder->getBindings(), $unserialize->getBindings());
    }

    /** @test */
    public function it_can_serialize_eloquent()
    {
        $builder = User::query();

        $serialized = (new Serialize())($builder);
        $unserialize = (new Unserialize())($serialized);

        $this->assertSame($builder->toSql(), $unserialize->toSql());
        $this->assertSame($builder->getBindings(), $unserialize->getBindings());
    }

    /** @test */
    public function it_can_serialize_queries()
    {
        $builder = User::query()->getQuery();

        $serialized = (new Serialize())($builder);
        $unserialize = (new Unserialize())($serialized);

        $this->assertSame($builder->toSql(), $unserialize->toSql());
        $this->assertSame($builder->getBindings(), $unserialize->getBindings());
    }
}
