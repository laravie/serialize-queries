<?php

namespace Laravie\SerializesQuery\Tests\Feature;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Laravie\SerializesQuery\Tests\Models\Comment;
use Laravie\SerializesQuery\Tests\Models\Post;
use Laravie\SerializesQuery\Tests\Models\User;
use Laravie\SerializesQuery\Tests\TestCase;
use Mockery as m;
use function Laravie\SerializesQuery\serialize;
use function Laravie\SerializesQuery\unserialize;

class QueryTest extends TestCase
{
    /** @test */
    public function it_can_serialize_a_basic_query_builder()
    {
        $builder = DB::table('users');
        $serialized = serialize($builder);

        $unserialize = unserialize($serialized);

        $this->assertSame('select * from "users"', $unserialize->toSql());

        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }

    /** @test */
    public function it_can_serialize_a_basic_query_builder_on_custom_connection()
    {
        $builder = DB::connection('mysql')->table('users');
        $serialized = serialize($builder);

        $unserialize = unserialize($serialized);

        $this->assertSame('select * from `users`', $unserialize->toSql());

        $this->assertSame($builder->toSql(), $unserialize->toSql());
        $this->assertSame('mysql', $unserialize->getConnection()->getName());
    }

    /** @test */
    public function it_can_serialize_a_basic_query_builder_with_wheres()
    {
        $builder = DB::table('users')->where('email', '=', 'crynobone@gmail.com');
        $serialized = serialize($builder);

        $unserialize = unserialize($serialized);

        $this->assertSame('select * from "users" where "email" = ?', $unserialize->toSql());

        $this->assertSame($builder->toSql(), $unserialize->toSql());
    }
}
