@extends('layouts.admin')
@section('title', 'Deliveries')
@section('subtitle', 'Manage sales and delivery receipts')

@section('content')

@php
    $totalCount = \App\Models\Delivery::count();
    $totalDistributed = \App\Models\Delivery::sum('total_amount') ?? 0;
    
    $totalCollected = 0;
    if (class_exists('App\Models\Payment') && \Illuminate\Support\Facades\Schema::hasTable('payments')) {
        $totalCollected = \App\Models\Payment::sum('amount') ?? 0;
    }
    
    $totalBalance = max(0, $totalDistributed - $totalCollected);
@endphp

<div class="w-full max-w-screen-2xl mx-auto space-y-6">
    
    {{-- 4 KPI BOXES --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="card p-5">
            <div class="text-[11px] font-bold text-ink-500 uppercase tracking-wider mb-1">Total Deliveries</div>
            <div class="text-2xl font-black text-ink-900">{{ number_format($totalCount) }}</div>
            <div class="text-xs text-ink-400 mt-2 font-medium"><i class="fas fa-truck text-blue-500 mr-1"></i> Delivery records</div>
        </div>

        <div class="card p-5">
            <div class="text-[11px] font-bold text-ink-500 uppercase tracking-wider mb-1">Distributed</div>
            <div class="text-2xl font-black text-ink-900">&#8369;{{ number_format($totalDistributed, 0) }}</div>
            <div class="text-xs text-amber-500 mt-2 font-medium"><i class="fas fa-box-open mr-1"></i> Total goods sent</div>
        </div>

        <div class="card p-5">
            <div class="text-[11px] font-bold text-ink-500 uppercase tracking-wider mb-1">Collected</div>
            <div class="text-2xl font-black text-emerald-600">&#8369;{{ number_format($totalCollected, 0) }}</div>
            <div class="text-xs text-emerald-500 mt-2 font-medium"><i class="fas fa-check-circle mr-1"></i> Payments received</div>
        </div>

        <div class="card p-5">
            <div class="text-[11px] font-bold text-ink-500 uppercase tracking-wider mb-1">Outstanding</div>
            <div class="text-2xl font-black text-red-600">&#8369;{{ number_format($totalBalance, 0) }}</div>
            <div class="text-xs text-red-500 mt-2 font-medium"><i class="fas fa-hourglass-half mr-1"></i> Still to collect</div>
        </div>
    </div>

    {{-- FILTER & SEARCH BAR --}}
    <div class="card p-4 flex flex-col md:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('deliveries.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative w-full md:w-64">
                <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-ink-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search DR # or customer..." class="input !pl-9">
            </div>
            <input type="date" name="date" value="{{ request('date') }}" class="input !w-auto cursor-pointer">
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request()->has('search') || request()->has('date'))
                <a href="{{ route('deliveries.index') }}" class="text-sm font-semibold text-ink-500 hover:text-brand-600">Clear</a>
            @endif
        </form>

        <a href="{{ route('deliveries.create') }}" class="btn btn-primary whitespace-nowrap">
            <i class="fas fa-plus"></i> New Delivery
        </a>
    </div>

    {{-- TABLE --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-clean">
                <thead>
                    <tr>
                        <th>DR Number</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th class="text-right">Total Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $delivery)
                        <tr class="group">
                            <td class="font-mono font-bold text-ink-900">{{ $delivery->dr_number }}</td>
                            <td>
                                <div class="font-bold text-ink-800">{{ $delivery->store->store_name ?? 'Unknown' }}</div>
                            </td>
                            <td class="text-ink-500 font-medium">{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</td>
                            <td class="text-right font-bold text-ink-900">&#8369;{{ number_format($delivery->total_amount, 2) }}</td>
                            <td class="text-center">
                                @if($delivery->status === 'delivered' || $delivery->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 uppercase tracking-wider">Delivered</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200 uppercase tracking-wider">{{ ucfirst($delivery->status ?? 'Pending') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('deliveries.show', $delivery) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-brand-600 bg-brand-50 hover:bg-brand-100 transition-colors shadow-sm" title="View Receipt"><i class="fas fa-eye text-xs"></i></a>
                                    <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST" onsubmit="return confirm('Delete this delivery?');">
                                        @csrf @method('DELETE')
                                        <button class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors shadow-sm" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-ink-400">
                                <i class="fas fa-truck text-4xl mb-3 text-ink-300"></i>
                                <p class="font-medium">No deliveries found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-ink-200 bg-ink-50">
            {{ $deliveries->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection