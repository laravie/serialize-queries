<?php

namespace Laravie\SerializesQuery\Tests\Feature;

use Laravie\SerializesQuery\Related;
use Laravie\SerializesQuery\Tests\Models\User;
use Laravie\SerializesQuery\Tests\TestCase;

class RelatedTest extends TestCase
{
    /** @test */
    public function it_can_serialize_a_basic_relation()
    {
        $relation = User::first()->posts();
        $serialized = Related::serialize($relation);

        $unserialize = Related::unserialize($serialized);

        $this->assertSame($relation->toSql(), $unserialize->toSql());
    }
}
