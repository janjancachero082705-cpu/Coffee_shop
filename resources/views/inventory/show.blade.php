@extends('layouts.admin')
@section('title', 'Stock Card — ' . $product->name)
@section('subtitle', $product->sku . ' · ' . $product->unit_type)

@section('content')

@php
    $stock   = $product->stock;
    $level   = $stock <= 0 ? 'out' : ($stock <= $product->reorder_level ? 'low' : 'ok');
    $tone    = [
        'out' => ['Out of stock', 'bg-red-50 text-red-700', 'bg-red-500'],
        'low' => ['Running low',  'bg-amber-50 text-amber-700', 'bg-amber-500'],
        'ok'  => ['In stock',     'bg-emerald-50 text-emerald-700', 'bg-emerald-500'],
    ][$level];
    $totalIn  = $movements->where('type', 'in')->sum('quantity');
    $totalOut = $movements->where('type', 'out')->sum('quantity');
@endphp

<style>
    .sc-kpi { padding:22px; display:flex; flex-direction:column; justify-content:space-between; gap:18px; min-height:132px; }
    .sc-kpi .k { font-size:14px; font-weight:500; color:#78716A; }
    .sc-kpi .v { font-size:clamp(24px,2.2vw,32px); font-weight:700; line-height:1.05; letter-spacing:-.02em; font-variant-numeric:tabular-nums; }
    .sc-kpi.hl { background:#FDE9D5; border-color:#F7D9BC; }
    .sc-kpi.hl .k { color:#8F5A24; }
    .sc-pill { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; padding:3px 10px; border-radius:999px; }
    .sc-type { display:inline-block; font-size:12px; font-weight:700; padding:3px 10px; border-radius:6px; }
    .sc-type.in { background:#E3F7EC; color:#18975A; } .sc-type.out { background:#FDE4E4; color:#D13838; } .sc-type.other { background:#EFEBE5; color:#57514B; }
    .sc-open td { background:#FFF3E5; }
</style>

{{-- Actions --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <a href="{{ route('inventory.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-ink-500 hover:text-brand-600 transition-colors">
        <i class="fas fa-arrow-left text-xs"></i> Back to inventory
    </a>
    <a href="{{ route('inventory.adjust', $product) }}" class="btn btn-primary"><i class="fas fa-sliders text-xs"></i>Adjust stock</a>
</div>

{{-- Product summary --}}
<section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6" aria-label="Product summary">
    <div class="card sc-kpi hl">
        <div class="flex items-center justify-between"><span class="k">Current stock</span><span class="sc-pill {{ $tone[1] }}"><span class="w-1.5 h-1.5 rounded-full {{ $tone[2] }}"></span>{{ $tone[0] }}</span></div>
        <div class="v">{{ number_format($stock, 2) }} <span class="text-sm font-semibold text-ink-500">{{ $product->unit_type }}</span></div>
    </div>
    <div class="card sc-kpi">
        <span class="k">Unit cost</span>
        <div class="v">&#8369;{{ number_format($product->cost_price, 2) }}</div>
    </div>
    <div class="card sc-kpi">
        <span class="k">Total value</span>
        <div class="v" style="color:#E86100">&#8369;{{ number_format($stock * $product->cost_price, 2) }}</div>
    </div>
    <div class="card sc-kpi">
        <span class="k">Reorder level</span>
        <div class="v">{{ number_format($product->reorder_level, 2) }} <span class="text-sm font-semibold text-ink-500">{{ $product->unit_type }}</span></div>
    </div>
</section>

{{-- Movements --}}
<section class="card overflow-hidden">
    <div class="p-5 border-b border-ink-100 flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
            <h2 class="text-base font-extrabold text-ink-900">Stock card</h2>
            <p class="text-sm text-ink-500 mt-0.5">
                {{ $movements->count() }} {{ \Illuminate\Support\Str::plural('movement', $movements->count()) }}
                @if($movements->count() > 0)
                    &middot; <span class="font-semibold text-emerald-600">+{{ number_format($totalIn, 2) }} in</span>
                    &middot; <span class="font-semibold text-red-600">-{{ number_format($totalOut, 2) }} out</span>
                @endif
            </p>
        </div>

        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="from" class="block text-xs font-semibold text-ink-500 mb-1">From</label>
                <input type="date" id="from" name="from" value="{{ $from }}" class="input !w-auto">
            </div>
            <div>
                <label for="to" class="block text-xs font-semibold text-ink-500 mb-1">To</label>
                <input type="date" id="to" name="to" value="{{ $to }}" class="input !w-auto">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('inventory.show', $product) }}" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    @if($movements->count() > 0)
        <div class="overflow-x-auto">
            <table class="table-clean">
                <thead>
                    <tr>
                        <th scope="col" class="!pl-5">Date</th>
                        <th scope="col">Type</th>
                        <th scope="col">Reference</th>
                        <th scope="col" class="text-right">In</th>
                        <th scope="col" class="text-right">Out</th>
                        <th scope="col" class="text-right">Balance</th>
                        <th scope="col" class="!pr-5">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="sc-open">
                        <td class="!pl-5 text-sm font-semibold text-ink-700" colspan="5">
                            <i class="fas fa-flag text-brand-500 mr-2 text-xs"></i>Opening balance <span class="font-medium text-ink-500">(before {{ \Carbon\Carbon::parse($from)->format('M d, Y') }})</span>
                        </td>
                        <td class="text-right font-extrabold text-ink-900 tabular-nums">{{ number_format($openingBalance, 2) }}</td>
                        <td class="!pr-5"></td>
                    </tr>

                    @foreach($movements as $m)
                        <tr>
                            <td class="!pl-5 whitespace-nowrap text-ink-600">{{ $m->movement_date->format('M d, Y') }}</td>
                            <td><span class="sc-type {{ in_array($m->type, ['in','out']) ? $m->type : 'other' }}">{{ $m->type_label }}</span></td>
                            <td class="text-ink-700 whitespace-nowrap">{{ $m->reference_label }}</td>
                            <td class="text-right font-bold text-emerald-600 tabular-nums">@if($m->type === 'in')+{{ number_format($m->quantity, 2) }}@endif</td>
                            <td class="text-right font-bold text-red-600 tabular-nums">@if($m->type === 'out')-{{ number_format($m->quantity, 2) }}@endif</td>
                            <td class="text-right font-extrabold text-ink-900 tabular-nums">{{ number_format($m->balance_after, 2) }}</td>
                            <td class="!pr-5 text-ink-500 max-w-xs truncate" title="{{ $m->notes }}">{{ $m->notes ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-16 px-4">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-brand-100 text-brand-600 flex items-center justify-center text-xl mb-4"><i class="fas fa-clock-rotate-left"></i></div>
            <h3 class="font-bold text-ink-900">No movements found</h3>
            <p class="text-sm text-ink-500 mt-1">Walay movements sa selected date range.</p>
            <a href="{{ route('inventory.show', $product) }}" class="btn btn-secondary mt-4">Reset dates</a>
        </div>
    @endif
</section>
@endsection