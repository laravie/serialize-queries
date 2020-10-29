<?php

namespace Laravie\SerializesQuery\Tests\Feature;

use Laravie\SerializesQuery\Serialize;
use Laravie\SerializesQuery\Tests\Models\User;
use Laravie\SerializesQuery\Tests\TestCase;

class SerializeTest extends TestCase
{
    /** @test */
    public function it_can_serialize_a_basic_query_builder()
    {
        $builder = User::query()->getQuery();

        $serialized = Serialize::execute($builder);

        $unserialized = Unserialize::execute($builder);

        $this->assertSame($builder->toSql(), $unserialized->toSql());
    }
    /** @test */
    public function it_can_serialize_a_basic_eloquent_builder()
    {
        $builder = User::query();

        $serialized = Serialize::execute($builder);

        $unserialized = Unserialize::execute($builder);

        $this->assertSame($builder->toSql(), $unserialized->toSql());
    }

    /** @test */
    public function it_can_serialize_a_basic_relation()
    {
        $relation = User::first()->posts();

        $serialized = Serialize::execute($relation);

        $unserialized = Unserialize::execute($relation);

        $this->assertSame($relation->toSql(), $unserialized->toSql());
    }
}
