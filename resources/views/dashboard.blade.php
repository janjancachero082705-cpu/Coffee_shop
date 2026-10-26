@extends('layouts.admin')
@section('title', 'Dashboard')
@section('subtitle', 'Business intelligence and financial overview')

@section('content')

{{-- ══════ TOP STATS CARDS (Modern Gradient Style) ══════ --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    {{-- 1. TOTAL SALES --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group hover:shadow-lg transition-all duration-300">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-50 rounded-full blur-2xl opacity-60 group-hover:opacity-100 transition-opacity"></div>
        <div class="relative z-10 flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Sales</p>
                <h3 class="text-3xl font-black text-slate-900 tracking-tight">&#8369;{{ number_format($totalSales, 0) }}</h3>
                <p class="text-[11px] font-medium text-emerald-600 mt-2 bg-emerald-50 inline-block px-2 py-1 rounded-md"><i class="fas fa-arrow-trend-up mr-1"></i> Revenue</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 text-white flex items-center justify-center text-xl shadow-lg shadow-emerald-500/30">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>
    
    {{-- 2. TOTAL PURCHASES --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group hover:shadow-lg transition-all duration-300">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-amber-50 rounded-full blur-2xl opacity-60 group-hover:opacity-100 transition-opacity"></div>
        <div class="relative z-10 flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Purchases</p>
                <h3 class="text-3xl font-black text-slate-900 tracking-tight">&#8369;{{ number_format($totalPurchases, 0) }}</h3>
                <p class="text-[11px] font-medium text-amber-600 mt-2 bg-amber-50 inline-block px-2 py-1 rounded-md"><i class="fas fa-cart-shopping mr-1"></i> Raw Materials</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 text-white flex items-center justify-center text-xl shadow-lg shadow-amber-500/30">
                <i class="fas fa-boxes-stacked"></i>
            </div>
        </div>
    </div>

    {{-- 3. TOTAL EXPENSES --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group hover:shadow-lg transition-all duration-300">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-rose-50 rounded-full blur-2xl opacity-60 group-hover:opacity-100 transition-opacity"></div>
        <div class="relative z-10 flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Expenses</p>
                <h3 class="text-3xl font-black text-slate-900 tracking-tight">&#8369;{{ number_format($totalExpenses, 0) }}</h3>
                <p class="text-[11px] font-medium text-rose-600 mt-2 bg-rose-50 inline-block px-2 py-1 rounded-md"><i class="fas fa-receipt mr-1"></i> Operations</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-400 to-rose-600 text-white flex items-center justify-center text-xl shadow-lg shadow-rose-500/30">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
        </div>
    </div>

    {{-- 4. NET PROFIT --}}
    <div class="rounded-2xl p-6 border relative overflow-hidden group hover:shadow-xl transition-all duration-300 {{ $netProfit >= 0 ? 'bg-gradient-to-br from-slate-800 to-slate-950 border-slate-700 shadow-slate-900/30' : 'bg-gradient-to-br from-red-900 to-red-950 border-red-800 shadow-red-900/30' }}">
        <div class="absolute -right-8 -bottom-8 text-white/5 transform rotate-12 group-hover:scale-110 transition-transform"><i class="fas fa-chart-pie fa-8x"></i></div>
        <div class="relative z-10 flex justify-between items-start">
            <div>
                <p class="text-xs font-bold {{ $netProfit >= 0 ? 'text-slate-400' : 'text-red-300' }} uppercase tracking-wider mb-1">Net Profit</p>
                <h3 class="text-3xl font-black tracking-tight {{ $netProfit >= 0 ? 'text-caramel-400' : 'text-red-400' }}">
                    {{ $netProfit >= 0 ? '' : '-' }}&#8369;{{ number_format(abs($netProfit), 0) }}
                </h3>
                <p class="text-[11px] font-medium {{ $netProfit >= 0 ? 'text-slate-400' : 'text-red-300' }} mt-2 bg-white/10 inline-block px-2 py-1 rounded-md backdrop-blur-sm"><i class="fas fa-scale-balanced mr-1"></i> Sales - (Purchases + Expenses)</p>
            </div>
            <div class="w-12 h-12 rounded-xl {{ $netProfit >= 0 ? 'bg-white/10 text-caramel-400 border border-white/10' : 'bg-red-500/20 text-red-300 border border-red-500/30' }} flex items-center justify-center text-xl backdrop-blur-md">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
</div>

{{-- ══════ SECONDARY STATS (Glassmorphism Row) ══════ --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-white shadow-sm text-slate-600 flex items-center justify-center"><i class="fas fa-truck-fast"></i></div>
        <div><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Deliveries</div><div class="text-xl font-black text-slate-800">{{ $totalDeliveries }}</div></div>
    </div>
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-white shadow-sm text-indigo-500 flex items-center justify-center"><i class="fas fa-store"></i></div>
        <div><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stores</div><div class="text-xl font-black text-slate-800">{{ $totalStores }}</div></div>
    </div>
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-white shadow-sm text-caramel-500 flex items-center justify-center"><i class="fas fa-box"></i></div>
        <div><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Products</div><div class="text-xl font-black text-slate-800">{{ $totalProducts }}</div></div>
    </div>
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-white shadow-sm text-rose-500 flex items-center justify-center"><i class="fas fa-money-bill-transfer"></i></div>
        <div><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Outflow</div><div class="text-xl font-black text-slate-800">&#8369;{{ number_format($totalOutflow, 0) }}</div></div>
    </div>
</div>

{{-- ══════ MAIN GRID (Chart + Low Stock) ══════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    {{-- SALES CHART (Mixed Chart) --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900 font-serif">Revenue Overview</h3>
                <p class="text-xs text-slate-500">Monthly performance trend</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-caramel-400"></span> <span class="text-xs font-bold text-slate-500">Sales</span>
            </div>
        </div>
        <div class="relative h-[320px] w-full">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- LOW STOCK ALERTS --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900 font-serif">Critical Stocks</h3>
            <a href="{{ route('inventory.index') }}" class="text-xs font-bold text-caramel-500 hover:text-caramel-600 bg-caramel-50 px-2 py-1 rounded-md">Inventory</a>
        </div>
        
        <div class="space-y-3 flex-1 overflow-y-auto pr-1">
            @forelse($lowStockVariants as $variant)
                <div class="flex items-center justify-between p-3 rounded-xl bg-rose-50/50 border border-rose-100 hover:bg-rose-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white border border-rose-200 flex items-center justify-center text-rose-500 shadow-sm">
                            <i class="fas fa-triangle-exclamation text-xs"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 line-clamp-1">{{ $variant->product->name ?? 'Unknown' }}</div>
                            <div class="text-[10px] text-rose-600 font-bold mt-0.5">{{ $variant->name }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-black text-rose-600">{{ $variant->stock }}</div>
                        <div class="text-[9px] text-rose-400 font-bold uppercase tracking-wider">Left</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-slate-400 text-sm flex flex-col items-center justify-center h-full">
                    <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mb-3"><i class="fas fa-check-double text-2xl text-emerald-500"></i></div>
                    <p class="font-bold text-slate-700">Stocks are healthy!</p>
                    <p class="text-xs mt-1">No items need restocking.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ══════ BOTTOM ROW (Recent Deliveries & Expenses) ══════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    {{-- RECENT DELIVERIES --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
            <h3 class="font-bold text-slate-900 font-serif flex items-center gap-2"><i class="fas fa-truck text-caramel-500"></i> Recent Deliveries</h3>
            <a href="{{ route('deliveries.index') }}" class="text-xs font-bold text-caramel-500 hover:text-caramel-600">View All</a>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm text-left">
                <thead class="bg-white border-b border-slate-100 text-slate-500 text-[10px] uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4">DR Number</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentDeliveries as $d)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900 font-mono text-xs">{{ $d->dr_number }}</td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ $d->store->store_name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4 text-right font-black text-slate-900">&#8369;{{ number_format($d->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-10 text-slate-400 text-sm">No deliveries yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RECENT EXPENSES --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
            <h3 class="font-bold text-slate-900 font-serif flex items-center gap-2"><i class="fas fa-receipt text-rose-500"></i> Recent Expenses</h3>
            <a href="{{ route('expenses.index') }}" class="text-xs font-bold text-caramel-500 hover:text-caramel-600">View All</a>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm text-left">
                <thead class="bg-white border-b border-slate-100 text-slate-500 text-[10px] uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentExpenses as $e)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 text-slate-600 text-xs font-medium">{{ \Carbon\Carbon::parse($e->expense_date)->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ $e->category }}</td>
                            <td class="px-6 py-4 text-right font-black text-rose-600">- &#8369;{{ number_format($e->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-10 text-slate-400 text-sm">No expenses yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    // Gradient for Bar Chart
    const barGradient = ctx.createLinearGradient(0, 0, 0, 300);
    barGradient.addColorStop(0, '#F59E0B'); // Caramel
    barGradient.addColorStop(1, '#D97706');

    // Gradient for Line Chart
    const lineGradient = ctx.createLinearGradient(0, 0, 0, 300);
    lineGradient.addColorStop(0, 'rgba(15, 23, 42, 0.2)'); // Slate
    lineGradient.addColorStop(1, 'rgba(15, 23, 42, 0)');

    // Combined Chart: Bar + Line
    new Chart(ctx, {
        type: 'bar', // Main type is Bar
        data: {
            labels: @json(collect($trend)->pluck('label')),
            datasets: [
                {
                    type: 'bar',
                    label: 'Sales (₱)',
                    data: @json(collect($trend)->pluck('sold')),
                    backgroundColor: barGradient,
                    borderRadius: 6, // Rounded bars
                    borderSkipped: false,
                    barPercentage: 0.6,
                    order: 2 // Draw behind the line
                },
                {
                    type: 'line', // Overlay line chart
                    label: 'Trend',
                    data: @json(collect($trend)->pluck('sold')),
                    borderColor: '#0F172A', // Slate 900
                    backgroundColor: lineGradient,
                    borderWidth: 3,
                    tension: 0.4, // Smooth curve
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#0F172A',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    order: 1 // Draw on top
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1E293B',
                    titleFont: { family: 'Inter', size: 13 },
                    bodyFont: { family: 'Inter', size: 14, weight: 'bold' },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: '#F1F5F9', drawTicks: false },
                    ticks: { 
                        color: '#64748B', 
                        font: { family: 'Inter', size: 11, weight: '600' }, 
                        padding: 10,
                        callback: v => '₱' + (v >= 1000 ? (v/1000)+'k' : v) 
                    }
                },
                x: {
                    border: { display: false },
                    grid: { display: false },
                    ticks: { 
                        color: '#64748B', 
                        font: { family: 'Inter', size: 11, weight: '500' }, 
                        padding: 10 
                    }
                }
            }
        }
    });
</script>
@endpush