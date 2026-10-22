<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    protected function applyDateRange(Builder $query, Request $request, string $column)
    {
        $preset = $request->input('date_preset');
        if ($preset && $preset !== 'all') {
            [$from, $to] = $this->resolveDatePreset($preset);
            if ($from && $to) {
                return $query->whereBetween($column, [$from, $to]);
            }
        }
        if ($request->filled('date_from')) $query->whereDate($column, '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate($column, '<=', $request->date_to);
        return $query;
    }

    protected function resolveDatePreset(string $preset): array
    {
        $now = now();
        return match($preset) {
            'today'        => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday'    => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            'this_week'    => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'last_week'    => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()],
            'this_month'   => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month'   => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            'this_quarter' => [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()],
            'last_quarter' => [$now->copy()->subQuarter()->startOfQuarter(), $now->copy()->subQuarter()->endOfQuarter()],
            'this_year'    => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'last_year'    => [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()],
            default        => [null, null],
        };
    }

    protected function applySearch(Builder $query, Request $request, array $columns)
    {
        if (!$request->filled('search')) return $query;
        $s = $request->search;
        return $query->where(function($q) use ($s, $columns) {
            foreach ($columns as $col) $q->orWhere($col, 'like', "%{$s}%");
        });
    }

    protected function applySort(Builder $query, Request $request, string $default, array $allowed)
    {
        $sort = $request->input('sort', $default);
        $dir = $request->input('direction', 'desc');
        if (!in_array($sort, $allowed)) $sort = $default;
        if (!in_array($dir, ['asc','desc'])) $dir = 'desc';
        return $query->orderBy($sort, $dir);
    }
}