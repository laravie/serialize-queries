<?php

namespace Laravie\SerializesQuery\Tests\Feature;

use function Laravie\SerializesQuery\serialize;
use Laravie\SerializesQuery\Tests\TestCase;
use function Laravie\SerializesQuery\unserialize;

class HelpersTest extends TestCase
{
    /** @test */
    public function it_cannot_serialize_an_object()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Unable to serialize $builder.');

        serialize(new \Illuminate\Support\Fluent());
    }

    /** @test */
    public function it_cannot_unserialize_other_object()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Unable to unserialize $payload.');

        unserialize(new \Illuminate\Support\Fluent());
    }
}
