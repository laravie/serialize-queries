<?php

namespace Laravie\SerializesQuery\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Get default bindings.
     */
    protected function defaultBindings(array $override = []): array
    {
        $bindings = [
            'select' => [], 'from' => [], 'join' => [], 'where' => [],
            'groupBy' => [], 'having' => [], 'order' => [],
            'union' => [], 'unionOrder' => [],
        ];

        foreach ($override as $key => $value) {
            $bindings[$key] = $value;
        }

        return $bindings;
    }
}
