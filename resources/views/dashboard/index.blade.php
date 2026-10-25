@extends('layouts.admin')

@section('title', 'Good Morning, ' . (Auth::user()->name ?? 'Admin'))
@section('subtitle', 'Your morning sets up the success of your day!')

@section('content')

{{-- ══════ TOP CARDS ══════ --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    {{-- Distributed --}}
    <div class="card p-6 flex items-center gap-4 border-l-4 border-l-caramel-400">
        <div class="w-14 h-14 rounded-2xl bg-caramel-50 text-caramel-500 flex items-center justify-center text-2xl flex-shrink-0 shadow-sm">
            <i class="fas fa-truck"></i>
        </div>
        <div>
            <div class="text-[11px] text-espresso-500 font-bold uppercase tracking-wider mb-1">Distributed</div>
            <div class="text-2xl font-bold text-espresso-900">&#8369;{{ number_format($distributed, 0) }}</div>
            <div class="text-[11px] text-espresso-400 mt-1 font-medium">{{ $distributedCount }} deliveries</div>
        </div>
    </div>

    {{-- Sold --}}
    <div class="card p-6 flex items-center gap-4 border-l-4 border-l-emerald-400">
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-2xl flex-shrink-0 shadow-sm">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div>
            <div class="text-[11px] text-espresso-500 font-bold uppercase tracking-wider mb-1">Sold (Reports)</div>
            <div class="text-2xl font-bold text-espresso-900">&#8369;{{ number_format($sold, 0) }}</div>
        </div>
    </div>

    {{-- Collected --}}
    <div class="card p-6 flex items-center gap-4 border-l-4 border-l-blue-400">
        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-2xl flex-shrink-0 shadow-sm">
            <i class="fas fa-wallet"></i>
        </div>
        <div>
            <div class="text-[11px] text-espresso-500 font-bold uppercase tracking-wider mb-1">Collected</div>
            <div class="text-2xl font-bold text-espresso-900">&#8369;{{ number_format($collected, 0) }}</div>
        </div>
    </div>

    {{-- Outstanding --}}
    <div class="p-6 rounded-2xl bg-espresso-800 text-white shadow-xl shadow-espresso-800/20 flex items-center justify-between relative overflow-hidden">
        <!-- Decorative Background -->
        <div class="absolute -right-4 -top-4 text-espresso-700/50 opacity-50">
            <i class="fas fa-coffee fa-5x"></i>
        </div>
        <div class="relative z-10">
            <div class="text-[11px] text-caramel-300 font-bold uppercase tracking-wider mb-1">Outstanding</div>
            <div class="text-2xl font-bold">&#8369;{{ number_format($outstanding, 0) }}</div>
        </div>
        <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-xl relative z-10 backdrop-blur-sm border border-white/20">
            <i class="fas fa-exclamation text-caramel-400"></i>
        </div>
    </div>

</div>

{{-- ══════ MAIN GRID (Chart + Side Panel) ══════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- CHART & BAR GRAPH --}}
    <div class="lg:col-span-2 card p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-espresso-900 font-serif">Sales Activities</h3>
                <p class="text-xs text-espresso-400">Your sales performance over time</p>
            </div>
            <div class="flex gap-2 text-xs font-medium text-espresso-500 bg-espresso-50 p-1 rounded-lg border border-espresso-100">
                <button class="px-3 py-1.5 rounded-md hover:bg-white hover:shadow-sm transition">Week</button>
                <button class="px-3 py-1.5 rounded-md hover:bg-white hover:shadow-sm transition">Month</button>
                <button class="px-3 py-1.5 rounded-md bg-white shadow-sm text-caramel-500 font-bold">Year</button>
            </div>
        </div>
        <div class="relative h-[300px] w-full">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- STORES LIST --}}
    <div class="card p-6 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-espresso-900 font-serif">Store Balances</h3>
            <a href="{{ route('stores.index') }}" class="text-xs text-caramel-500 hover:text-caramel-600 font-bold">View All</a>
        </div>
        <div class="space-y-2 flex-1 overflow-y-auto pr-2">
            @forelse($storeBalances->take(5) as $sb)
                <div class="flex items-center justify-between p-3 rounded-xl hover:bg-espresso-50 transition cursor-pointer border border-transparent hover:border-espresso-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-espresso-100 text-espresso-700 flex items-center justify-center font-bold text-xs shadow-inner">
                            {{ strtoupper(substr($sb->store->store_name ?? 'S', 0, 2)) }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-espresso-900">{{ $sb->store->store_name ?? 'Unknown' }}</div>
                            <div class="text-[11px] text-espresso-400 font-medium">Sold: &#8369;{{ number_format($sb->total_sold, 0) }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold {{ $sb->total_balance > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                            &#8369;{{ number_format($sb->total_balance, 0) }}
                        </div>
                        <div class="text-[10px] text-espresso-400 font-semibold uppercase tracking-wider">Balance</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-espresso-400 text-sm flex flex-col items-center justify-center h-full">
                    <i class="fas fa-store-slash text-3xl mb-3 text-espresso-200"></i>
                    <p>No store balances yet.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ══════ BOTTOM ROW ══════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- RECENT SALES REPORTS --}}
    <div class="lg:col-span-2 card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-espresso-900 font-serif">Recent Sales Reports</h3>
            <a href="{{ route('sales-reports.index') }}" class="text-xs text-caramel-500 hover:text-caramel-600 font-bold">View All</a>
        </div>
        
        @if($recentReports->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-espresso-400 text-[11px] uppercase tracking-wider border-b border-espresso-100">
                            <th class="pb-3 font-bold">Report #</th>
                            <th class="pb-3 font-bold">Store</th>
                            <th class="pb-3 font-bold">Period</th>
                            <th class="pb-3 text-right font-bold">Total</th>
                            <th class="pb-3 text-center font-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-espresso-800">
                        @foreach($recentReports as $r)
                            <tr class="border-b border-espresso-50 hover:bg-espresso-50/50 transition-colors">
                                <td class="py-3.5 font-bold text-xs text-caramel-600">{{ $r->report_number }}</td>
                                <td class="py-3.5 font-medium">{{ $r->store->store_name ?? '—' }}</td>
                                <td class="py-3.5 text-xs text-espresso-500">{{ $r->period_from->format('M d') }} - {{ $r->period_to->format('M d, Y') }}</td>
                                <td class="py-3.5 text-right font-bold text-espresso-900">&#8369;{{ number_format($r->total_sales, 0) }}</td>
                                <td class="py-3.5 text-center">
                                    <span class="badge badge-{{ $r->status === 'paid' || $r->status === 'closed' ? 'success' : ($r->status === 'partial' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($r->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-espresso-400">
                <i class="fas fa-file-invoice text-4xl mb-3 block text-espresso-200"></i>
                <p class="text-sm font-medium">No recent sales reports.</p>
            </div>
        @endif
    </div>

    {{-- RECENT DELIVERIES / EVENTS --}}
    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-espresso-900 font-serif">Recent Deliveries</h3>
            <a href="{{ route('deliveries.index') }}" class="text-xs text-caramel-500 hover:text-caramel-600 font-bold">View All</a>
        </div>

        <div class="space-y-4">
            @forelse($recentDeliveries as $d)
                <div class="flex gap-3 items-center p-2 rounded-xl hover:bg-espresso-50 transition-colors border border-transparent hover:border-espresso-100">
                    <div class="w-10 h-10 rounded-xl bg-caramel-50 text-caramel-500 flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i class="fas fa-truck text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-espresso-900 truncate">{{ $d->store->store_name ?? 'Unknown' }}</div>
                        <div class="text-[11px] text-espresso-400 font-medium">{{ $d->dr_number }} • {{ $d->delivery_date->format('M d') }}</div>
                    </div>
                    <div class="text-xs font-bold text-espresso-800 bg-espresso-100 px-2 py-1 rounded-md">
                        &#8369;{{ number_format($d->total_amount, 0) }}
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-espresso-400 text-sm">
                    No recent deliveries.
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    // Gradient for Bar Chart
    const barGradient = ctx.createLinearGradient(0, 0, 0, 300);
    barGradient.addColorStop(0, '#E8A33D'); // Caramel
    barGradient.addColorStop(1, '#D4901F');

    // Gradient for Line Chart
    const lineGradient = ctx.createLinearGradient(0, 0, 0, 300);
    lineGradient.addColorStop(0, 'rgba(44, 24, 16, 0.2)'); // Espresso
    lineGradient.addColorStop(1, 'rgba(44, 24, 16, 0)');

    // You requested a chart and bar graph. We will use a Combined Chart.
    new Chart(ctx, {
        type: 'bar', // Main type is Bar
        data: {
            labels: @json(collect($trend)->pluck('label')),
            datasets: [
                {
                    type: 'bar',
                    label: 'Sales (&#8369;)',
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
                    borderColor: '#2C1810', // Espresso
                    backgroundColor: lineGradient,
                    borderWidth: 3,
                    tension: 0.4, // Smooth curve
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2C1810',
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
                    backgroundColor: '#2C1810',
                    titleFont: { family: 'Inter', size: 13 },
                    bodyFont: { family: 'Inter', size: 14, weight: 'bold' },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return ' &#8369;' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: '#F5F0E8', drawTicks: false }, // Espresso 100
                    ticks: { 
                        color: '#8B6F4E', // Espresso 500
                        font: { family: 'Inter', size: 11, weight: '600' }, 
                        padding: 10,
                        callback: v => '&#8369;' + (v >= 1000 ? (v/1000)+'k' : v) 
                    }
                },
                x: {
                    border: { display: false },
                    grid: { display: false },
                    ticks: { 
                        color: '#8B6F4E', 
                        font: { family: 'Inter', size: 11, weight: '500' }, 
                        padding: 10 
                    }
                }
            }
        }
    });
</script>
@endpush