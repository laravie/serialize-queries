<?php

namespace Laravie\SerializesQuery\Tests\Feature;

use Laravie\SerializesQuery\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

use function Laravie\SerializesQuery\serialize;
use function Laravie\SerializesQuery\unserialize;

class HelpersTest extends TestCase
{
    #[Test]
    public function it_cannot_serialize_an_object()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Unable to serialize $builder.');

        serialize(new \Illuminate\Support\Fluent());
    }

    #[Test]
    public function it_cannot_unserialize_other_object()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Unable to unserialize $payload.');

        unserialize(new \Illuminate\Support\Fluent());
    }
}
