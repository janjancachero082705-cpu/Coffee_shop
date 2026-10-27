@extends('layouts.admin')
@section('title', 'Delivery Receipt')
@section('subtitle', $delivery->dr_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    {{-- HEADER --}}
    <div class="card p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-black text-ink-900">{{ $delivery->dr_number }}</h2>
            <div class="flex items-center gap-3 mt-2">
                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold {{ $delivery->status === 'delivered' || $delivery->status === 'completed' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-amber-50 text-amber-600 border-amber-200' }} border uppercase tracking-wider">
                    {{ ucfirst($delivery->status ?? 'Pending') }}
                </span>
                <span class="text-sm text-ink-500 font-medium"><i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($delivery->delivery_date)->format('F d, Y') }}</span>
            </div>
        </div>
        
        <div class="text-right">
            <div class="text-[10px] font-bold text-ink-500 uppercase tracking-wider mb-1">Customer</div>
            <div class="text-lg font-bold text-ink-900">{{ $delivery->store->store_name ?? 'Unknown' }}</div>
            <div class="text-xs text-ink-500">{{ $delivery->store->address ?? '' }}</div>
        </div>
    </div>

    {{-- ITEMS TABLE --}}
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-ink-200 bg-ink-50 flex items-center justify-between">
            <h3 class="font-bold text-ink-800">Items Delivered</h3>
            <a href="{{ url()->previous() }}" class="btn btn-secondary !py-1.5 !px-3 !text-xs"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="table-clean">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($delivery->items as $item)
                        <tr>
                            <td>
                                <div class="font-bold text-ink-900">{{ $item->variant->product->name ?? 'Unknown' }}</div>
                                <div class="text-[10px] text-ink-500">{{ $item->variant->name ?? '' }}</div>
                            </td>
                            <td class="text-center font-bold text-ink-800">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-right text-ink-600">₱{{ number_format($item->price, 2) }}</td>
                            <td class="text-right font-bold text-ink-900">₱{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-6 text-ink-400">No items found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- TOTALS --}}
        <div class="p-6 bg-ink-50 border-t border-ink-200 flex flex-col items-end gap-2">
            <div class="flex justify-between w-full md:w-64 text-sm">
                <span class="text-ink-500 font-medium">Subtotal</span>
                <span class="font-bold text-ink-800">₱{{ number_format($delivery->total_amount, 2) }}</span>
            </div>
            @if($delivery->discount > 0)
                <div class="flex justify-between w-full md:w-64 text-sm">
                    <span class="text-brand-600 font-medium">Discount</span>
                    <span class="font-bold text-brand-600">-₱{{ number_format($delivery->discount, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between w-full md:w-64 text-lg border-t border-ink-200 pt-2 mt-1">
                <span class="font-black text-ink-900">Total Amount</span>
                <span class="font-black text-brand-600">₱{{ number_format($delivery->total_amount - $delivery->discount, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection