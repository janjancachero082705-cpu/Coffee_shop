@extends('layouts.admin')
@section('title', 'Stock Card — ' . $product->name)
@section('subtitle', $product->sku . ' · ' . $product->unit_type)
@section('header-actions')
<a href="{{ route('inventory.adjust', $product) }}" class="px-4 py-2 bg-gradient-to-r from-gold-600 to-gold-800 text-white font-bold rounded-lg text-sm">
    <i class="fas fa-sliders-h mr-1"></i> Adjust Stock
</a>
@endsection
@section('content')

{{-- PRODUCT INFO --}}
<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-card p-5">
        <div class="text-xs font-bold text-coffee-500 uppercase mb-1">Current Stock</div>
        <div class="text-3xl font-black {{ $product->stock <= 0 ? 'text-red-600' : ($product->stock <= $product->reorder_level ? 'text-amber-600' : 'text-emerald-600') }}">
            {{ number_format($product->stock, 2) }} <span class="text-sm font-medium text-coffee-500">{{ $product->unit_type }}</span>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-card p-5">
        <div class="text-xs font-bold text-coffee-500 uppercase mb-1">Unit Cost</div>
        <div class="text-2xl font-black text-coffee-900">&#8369;{{ number_format($product->cost_price, 2) }}</div>
    </div>
    <div class="bg-white rounded-2xl shadow-card p-5">
        <div class="text-xs font-bold text-coffee-500 uppercase mb-1">Total Value</div>
        <div class="text-2xl font-black text-gold-600">&#8369;{{ number_format($product->stock * $product->cost_price, 2) }}</div>
    </div>
    <div class="bg-white rounded-2xl shadow-card p-5">
        <div class="text-xs font-bold text-coffee-500 uppercase mb-1">Reorder Level</div>
        <div class="text-2xl font-black text-coffee-600">{{ number_format($product->reorder_level, 2) }}</div>
    </div>
</div>

{{-- DATE FILTER --}}
<div class="bg-white rounded-2xl shadow-card p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div><label class="block text-xs font-bold text-coffee-500 uppercase mb-1">From</label>
            <input type="date" name="from" value="{{ $from }}" class="px-3 py-2 border border-coffee-200 rounded-lg text-sm"></div>
        <div><label class="block text-xs font-bold text-coffee-500 uppercase mb-1">To</label>
            <input type="date" name="to" value="{{ $to }}" class="px-3 py-2 border border-coffee-200 rounded-lg text-sm"></div>
        <button class="px-4 py-2 bg-coffee-800 text-white rounded-lg font-semibold text-sm">Filter</button>
        <a href="{{ route('inventory.show', $product) }}" class="px-4 py-2 bg-coffee-100 text-coffee-700 rounded-lg font-semibold text-sm">Reset</a>
    </form>
</div>

{{-- MOVEMENTS TABLE --}}
<div class="bg-white rounded-2xl shadow-card overflow-hidden">
    <div class="px-6 py-4 border-b border-coffee-100 flex items-center justify-between">
        <h3 class="font-display text-lg font-bold text-coffee-900">Stock Card</h3>
        <span class="text-xs text-coffee-500">{{ $movements->count() }} movements</span>
    </div>

    @if($movements->count() > 0)
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-coffee-50/50 border-b border-coffee-100">
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-coffee-500 uppercase">Date</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-coffee-500 uppercase">Type</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-coffee-500 uppercase">Reference</th>
                    <th class="text-right px-4 py-3 text-[10px] font-bold text-coffee-500 uppercase">In</th>
                    <th class="text-right px-4 py-3 text-[10px] font-bold text-coffee-500 uppercase">Out</th>
                    <th class="text-right px-4 py-3 text-[10px] font-bold text-coffee-500 uppercase">Balance</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-coffee-500 uppercase">Notes</th>
                </tr>
            </thead>
            <tbody>
                {{-- Opening balance row --}}
                <tr class="bg-coffee-50/30 border-b border-coffee-100">
                    <td class="px-4 py-2 text-xs font-semibold" colspan="5">Opening Balance (before {{ \Carbon\Carbon::parse($from)->format('M d, Y') }})</td>
                    <td class="px-4 py-2 text-right font-black text-coffee-800">{{ number_format($openingBalance, 2) }}</td>
                    <td></td>
                </tr>

                @foreach($movements as $m)
                    <tr class="border-b border-coffee-50 hover:bg-coffee-50/40">
                        <td class="px-4 py-3 text-xs text-coffee-600">{{ $m->movement_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $m->type_badge }}">{{ $m->type_label }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-coffee-700">{{ $m->reference_label }}</td>
                        <td class="px-4 py-3 text-right font-bold text-emerald-600">
                            @if($m->type === 'in')
                                +{{ number_format($m->quantity, 2) }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-red-600">
                            @if($m->type === 'out')
                                -{{ number_format($m->quantity, 2) }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-black text-coffee-900">{{ number_format($m->balance_after, 2) }}</td>
                        <td class="px-4 py-3 text-xs text-coffee-600 truncate max-w-xs">{{ $m->notes ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="text-center py-12 text-coffee-300">
            <i class="fas fa-history text-4xl mb-2 block"></i>
            <p class="text-sm text-coffee-500">Walay movements sa selected date range</p>
        </div>
    @endif
</div>
@endsection