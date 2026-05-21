<?php

namespace App\Http\Concerns;

trait ResolvesPagination
{
    protected function resolvePage(): int
    {
        return max(1, (int) request('page', 1));
    }

    protected function resolvePerPage(int $default = 25, int $max = 100): int
    {
        return min($max, max(1, (int) request('per_page', $default)));
    }
}
