<?php

namespace Laravie\SerializesQuery\Tests;

use Illuminate\Foundation\Application;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Get default bindings.
     */
    protected function defaultBindings(array $override = []): array
    {
        if (version_compare(Application::VERSION, '6.0.0', '>=')) {
            $bindings = [
                'select' => [], 'from' => [], 'join' => [], 'where' => [],
                'groupBy' => [], 'having' => [], 'order' => [],
                'union' => [], 'unionOrder' => [],
            ];
        } else {
            $bindings = [
                'select' => [], 'from' => [], 'join' => [], 'where' => [],
                'having' => [], 'order' => [], 'union' => [],
            ];
        }

        foreach ($override as $key => $value) {
            $bindings[$key] = $value;
        }

        return $bindings;
    }
}
