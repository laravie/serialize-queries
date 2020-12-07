<?php

namespace Laravie\SerializesQuery\Tests;

use Laravie\SerializesQuery\SerializesQueryProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [SerializesQueryProvider::class];
    }
}
