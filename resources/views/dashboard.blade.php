@extends('layouts.admin')
@section('title', 'Dashboard')
@section('subtitle', 'Business overview for this month')

@section('content')

{{-- TOP STATS CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    
    {{-- 1. SALES --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Sales (In)</div>
            <div class="text-3xl font-black text-slate-900">&#8369;{{ number_format($totalSales, 0) }}</div>
            <div class="text-xs text-slate-400 mt-2 font-medium"><i class="fas fa-truck text-blue-400 mr-1"></i> {{ $distributedCount }} Deliveries</div>
        </div>
    </div>
    
    {{-- 2. PURCHASES --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Purchases (Out)</div>
            <div class="text-3xl font-black text-amber-600">&#8369;{{ number_format($totalPurchases, 0) }}</div>
            <div class="text-xs text-slate-400 mt-2 font-medium"><i class="fas fa-shopping-cart text-amber-400 mr-1"></i> Raw Materials</div>
        </div>
    </div>

    {{-- 3. EXPENSES --}}
    <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-200 relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Expenses (Out)</div>
            <div class="text-3xl font-black text-red-600">&#8369;{{ number_format($totalExpenses, 0) }}</div>
            <div class="text-xs text-slate-400 mt-2 font-medium"><i class="fas fa-receipt text-red-400 mr-1"></i> Operational Costs</div>
        </div>
    </div>

    {{-- 4. PROFIT --}}
    <div class="rounded-xl shadow-sm p-5 border {{ $netProfit >= 0 ? 'bg-slate-900 border-slate-800' : 'bg-red-900 border-red-800' }} relative overflow-hidden group">
        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-800 opacity-30 group-hover:scale-110 transition-transform"><i class="fas fa-chart-line fa-5x"></i></div>
        <div class="relative z-10">
            <div class="text-[11px] font-bold {{ $netProfit >= 0 ? 'text-slate-400' : 'text-red-300' }} uppercase tracking-wider mb-1">Net Profit</div>
            <div class="text-3xl font-black {{ $netProfit >= 0 ? 'text-emerald-400' : 'text-red-300' }}">
                {{ $netProfit >= 0 ? '' : '-' }}&#8369;{{ number_format(abs($netProfit), 0) }}
            </div>
            <div class="text-xs {{ $netProfit >= 0 ? 'text-slate-400' : 'text-red-300' }} mt-2 font-medium">Sales - Purchases - Expenses</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- CHART --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900 font-serif">Sales Activities</h3>
                <p class="text-xs text-slate-400">Your sales performance over time</p>
            </div>
        </div>
        <div class="relative h-[300px] w-full">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- LOW STOCK ALERTS --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900 font-serif">Low Stock Alerts</h3>
            <a href="{{ route('inventory.index') }}" class="text-xs text-caramel-500 hover:text-caramel-600 font-bold">Inventory</a>
        </div>
        
        <div class="space-y-3 flex-1 overflow-y-auto pr-2">
            @forelse($lowStockVariants as $variant)
                <div class="flex items-center justify-between p-3 rounded-xl bg-red-50/50 border border-red-100">
                    <div>
                        <div class="text-sm font-bold text-slate-900">{{ $variant->product->name ?? 'Unknown' }}</div>
                        <div class="text-[10px] text-red-600 font-bold mt-0.5">{{ $variant->name }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-black text-red-600">{{ $variant->stock }}</div>
                        <div class="text-[9px] text-red-400 font-bold uppercase tracking-wider">Left</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 text-sm flex flex-col items-center justify-center h-full">
                    <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-check-circle text-xl text-emerald-500"></i>
                    </div>
                    <p class="font-medium">All stocks are healthy!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- RECENT DELIVERIES --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/30 flex justify-between items-center">
        <h3 class="font-bold text-slate-900 font-serif">Recent Deliveries</h3>
        <a href="{{ route('deliveries.index') }}" class="text-xs text-caramel-500 hover:text-caramel-600 font-bold">View All</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-white border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider font-bold">
                <tr>
                    <th class="px-6 py-3">DR Number</th>
                    <th class="px-6 py-3">Store</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3 text-right">Amount</th>
                    <th class="px-6 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentDeliveries as $d)
                    <tr class="hover:bg-slate-50/50 transition-colors odd:bg-white even:bg-slate-50/20">
                        <td class="px-6 py-4 font-bold text-slate-900 font-mono">{{ $d->dr_number }}</td>
                        <td class="px-6 py-4 font-medium text-slate-700">{{ $d->store->store_name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ \Carbon\Carbon::parse($d->delivery_date)->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-900">&#8369;{{ number_format($d->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($d->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-md text-[10px] font-bold border border-amber-200 uppercase tracking-wide"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-md text-[10px] font-bold border border-emerald-200 uppercase tracking-wide"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Paid</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-slate-500 text-sm">No recent deliveries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const barGradient = ctx.createLinearGradient(0, 0, 0, 300);
    barGradient.addColorStop(0, '#F59E0B');
    barGradient.addColorStop(1, '#D97706');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json(collect($trend)->pluck('label')),
            datasets: [{
                label: 'Sales',
                data: @json(collect($trend)->pluck('sold')),
                backgroundColor: barGradient,
                borderRadius: 4,
                barPercentage: 0.5,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#F1F5F9' }, ticks: { color: '#64748B', font: {size: 11} } },
                x: { grid: { display: false }, ticks: { color: '#64748B', font: {size: 11} } }
            }
        }
    });
</script>
@endpush