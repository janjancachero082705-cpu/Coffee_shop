@extends('layouts.admin')
@section('title', $store->store_name)
@section('subtitle', $store->code)

@section('header-actions')
<a href="{{ route('stores.edit', $store) }}" class="px-4 py-2 bg-amber-600 text-white font-semibold rounded-lg hover:bg-amber-700">
    <i class="fas fa-edit mr-1"></i> Edit
</a>
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- STORE INFO --}}
    <div class="lg:col-span-2 card p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-2xl font-black text-gray-800">{{ $store->store_name }}</h2>
                <p class="text-sm text-gray-500">{{ $store->owner_name }}</p>
            </div>
            <div class="flex gap-2">
                @if($store->customer_type === 'suki')
                    <span class="badge badge-suki">⭐ Suki</span>
                @elseif($store->customer_type === 'friend')
                    <span class="badge badge-friend">👥 Friend</span>
                @else
                    <span class="badge badge-regular">Regular</span>
                @endif
                <span class="badge badge-{{ $store->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($store->status) }}</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm border-t border-gray-100 pt-4">
            <div>
                <div class="text-xs text-gray-500 uppercase font-bold mb-1">Contact</div>
                <div class="font-semibold">{{ $store->contact_number }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase font-bold mb-1">Email</div>
                <div class="font-semibold">{{ $store->email ?? '—' }}</div>
            </div>
            <div class="col-span-2">
                <div class="text-xs text-gray-500 uppercase font-bold mb-1">Address</div>
                <div class="font-semibold">
                    {{ $store->address }}
                    @if($store->barangay), {{ $store->barangay }}@endif
                    @if($store->city), {{ $store->city }}@endif
                </div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase font-bold mb-1">Discount</div>
                <div class="text-xl font-black text-amber-600">{{ number_format($store->default_discount_percent, 2) }}%</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase font-bold mb-1">Credit Limit</div>
                <div class="text-xl font-black">&#8369;{{ number_format($store->credit_limit, 2) }}</div>
            </div>
        </div>

        @if($store->notes)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="text-xs text-gray-500 uppercase font-bold mb-1">Notes</div>
                <p class="text-sm text-gray-700">{{ $store->notes }}</p>
            </div>
        @endif
    </div>

    {{-- STATS --}}
    <div class="space-y-4">
        <div class="card p-5">
            <div class="text-xs font-bold text-gray-500 uppercase mb-1">Total Deliveries</div>
            <div class="text-3xl font-black text-blue-600">{{ $stats['total_deliveries'] }}</div>
        </div>
        <div class="card p-5">
            <div class="text-xs font-bold text-gray-500 uppercase mb-1">Total Sales</div>
            <div class="text-2xl font-black text-gray-800">&#8369;{{ number_format($stats['total_sales'], 2) }}</div>
        </div>
        <div class="card p-5">
            <div class="text-xs font-bold text-gray-500 uppercase mb-1">Total Paid</div>
            <div class="text-2xl font-black text-green-600">&#8369;{{ number_format($stats['total_paid'], 2) }}</div>
        </div>
        <div class="card p-5" style="border-left: 4px solid #ef4444;">
            <div class="text-xs font-bold text-gray-500 uppercase mb-1">Outstanding</div>
            <div class="text-2xl font-black text-red-600">&#8369;{{ number_format($stats['outstanding'], 2) }}</div>
        </div>
    </div>
</div>

{{-- RECENT DELIVERIES --}}
<div class="card overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-bold text-gray-700">Recent Deliveries</h3>
        <a href="{{ route('deliveries.create') }}?store_id={{ $store->id }}" class="text-sm text-amber-600 font-semibold">
            + New Delivery
        </a>
    </div>

    @if($store->deliveryReceipts->count() > 0)
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-6 py-3">DR #</th>
                    <th class="text-left px-6 py-3">Date</th>
                    <th class="text-right px-6 py-3">Total</th>
                    <th class="text-right px-6 py-3">Balance</th>
                    <th class="text-center px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($store->deliveryReceipts as $dr)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-6 py-3 font-mono font-semibold">{{ $dr->dr_number }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $dr->delivery_date->format('M d, Y') }}</td>
                        <td class="px-6 py-3 text-right font-bold">&#8369;{{ number_format($dr->total_amount, 2) }}</td>
                        <td class="px-6 py-3 text-right {{ $dr->balance > 0 ? 'text-red-600 font-bold' : 'text-gray-400' }}">
                            &#8369;{{ number_format($dr->balance, 2) }}
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="badge {{ $dr->status === 'delivered' ? 'badge-success' : 'badge-warning' }}">
                                {{ $dr->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-truck text-4xl mb-2 block"></i>
            <p class="text-sm">Wala pay deliveries para sa store nga ni</p>
        </div>
    @endif
</div>

@endsection