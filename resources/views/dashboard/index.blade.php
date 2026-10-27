@extends('layouts.admin')
@section('title', 'Dashboard')
@section('subtitle', 'Overview of your business performance')

@section('content')

@php
    // Ensure safe variables para sa chart
    $chartExpenses = max(0, $totalExpenses);
    $chartPurchases = max(0, $totalPurchases);
    $chartProfit = max(0, $netProfit); 
@endphp

<div class="w-full max-w-screen-2xl mx-auto space-y-6 pb-10">
    
    {{-- ══════ 1. KPI CARDS ══════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        {{-- Distributed --}}
        <div class="card p-5 border-l-4 border-l-blue-500 hover:-translate-y-1 transition-transform duration-300">
            <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Distributed</div>
            <div class="text-2xl font-black text-ink-900">₱{{ number_format($distributed, 0) }}</div>
            <div class="text-[10px] text-ink-400 mt-1 font-medium">{{ $distributedCount }} deliveries</div>
        </div>

        {{-- Sold --}}
        <div class="card p-5 border-l-4 border-l-teal-500 hover:-translate-y-1 transition-transform duration-300">
            <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Sold</div>
            <div class="text-2xl font-black text-ink-900">₱{{ number_format($sold, 0) }}</div>
            <div class="text-[10px] text-teal-600 mt-1 font-medium">Total sales reported</div>
        </div>

        {{-- Collected --}}
        <div class="card p-5 border-l-4 border-l-cyan-500 hover:-translate-y-1 transition-transform duration-300">
            <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Collected</div>
            <div class="text-2xl font-black text-ink-900">₱{{ number_format($collected, 0) }}</div>
            <div class="text-[10px] text-cyan-600 mt-1 font-medium">Cash in hand</div>
        </div>

        {{-- Outstanding --}}
        <div class="card p-5 border-l-4 border-l-amber-500 hover:-translate-y-1 transition-transform duration-300">
            <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Outstanding</div>
            <div class="text-2xl font-black text-amber-600">₱{{ number_format($outstanding, 0) }}</div>
            <div class="text-[10px] text-amber-600 mt-1 font-medium">Still to collect</div>
        </div>

        {{-- Expenses --}}
        <div class="card p-5 border-l-4 border-l-red-500 hover:-translate-y-1 transition-transform duration-300">
            <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Expenses</div>
            <div class="text-2xl font-black text-red-600">₱{{ number_format($totalExpenses + $totalPurchases, 0) }}</div>
            <div class="text-[10px] text-red-500 mt-1 font-medium">Ops + Purchases</div>
        </div>

        {{-- Net Profit (GINAWANG GREEN) --}}
        <div class="card p-5 border-l-4 border-l-emerald-500 bg-emerald-50/30 hover:-translate-y-1 transition-transform duration-300">
            <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1">Cash Profit</div>
            <div class="text-2xl font-black text-emerald-700">₱{{ number_format($netProfit, 0) }}</div>
            <div class="text-[10px] text-emerald-600 mt-1 font-medium">Collected - Expenses</div>
        </div>
    </div>

    {{-- ══════ 2. CHARTS ══════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Sales Trend --}}
        <div class="lg:col-span-2 card p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-ink-900">Sales Trend</h3>
                <span class="text-xs font-bold text-brand-600 bg-brand-50 px-2 py-1 rounded">Last 6 Months</span>
            </div>
            <div class="relative h-[300px]">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Cash Flow Breakdown (Donut) --}}
        <div class="card p-6 flex flex-col">
            <h3 class="font-bold text-ink-900 mb-4">Cash Flow Breakdown</h3>
            
            <div class="relative flex-1 flex items-center justify-center min-h-[220px]">
                <canvas id="shareChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-[10px] font-bold text-ink-400 uppercase tracking-widest">Total Collected</span>
                    <span class="text-xl font-black text-ink-900">₱{{ number_format($collected, 0) }}</span>
                </div>
            </div>
            
            <div class="space-y-3 mt-4">
                <div class="flex justify-between items-center text-sm">
                    <span class="flex items-center gap-2 font-medium text-ink-600"><span class="w-2.5 h-2.5 rounded bg-red-500"></span> Expenses</span>
                    <span class="font-bold text-ink-900">₱{{ number_format($chartExpenses, 0) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="flex items-center gap-2 font-medium text-ink-600"><span class="w-2.5 h-2.5 rounded bg-amber-500"></span> Purchases</span>
                    <span class="font-bold text-ink-900">₱{{ number_format($chartPurchases, 0) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm border-t border-ink-100 pt-2 mt-2">
                    {{-- GINAWANG GREEN ANG LEGEND --}}
                    <span class="flex items-center gap-2 font-bold text-emerald-700"><span class="w-2.5 h-2.5 rounded bg-emerald-500"></span> Cash Profit</span>
                    <span class="font-black text-emerald-700">₱{{ number_format($chartProfit, 0) }}</span>
                </div>
                
                {{-- Outstanding Badge (Pahinumdom) --}}
                <div class="bg-amber-50 text-amber-700 text-[11px] font-bold px-3 py-2 rounded-lg border border-amber-100 flex justify-between items-center mt-2">
                    <span><i class="fas fa-clock mr-1"></i> Pending Collection</span>
                    <span>₱{{ number_format($outstanding, 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════ 3. RECENT TABLES ══════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Recent Deliveries --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-ink-200 bg-ink-50 flex justify-between items-center">
                <h3 class="font-bold text-ink-900">Recent Deliveries</h3>
                <a href="{{ route('deliveries.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Date</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentDeliveries as $d)
                            <tr>
                                <td class="font-bold text-ink-900">{{ $d->store->store_name ?? 'Unknown' }}</td>
                                <td class="text-ink-500">{{ \Carbon\Carbon::parse($d->delivery_date)->format('M d, Y') }}</td>
                                <td class="text-right font-bold text-ink-900">₱{{ number_format($d->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-8 text-ink-400">No deliveries yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Expenses --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-ink-200 bg-ink-50 flex justify-between items-center">
                <h3 class="font-bold text-ink-900">Recent Expenses</h3>
                <a href="{{ route('expenses.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Date</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentExpenses as $e)
                            <tr>
                                <td class="font-bold text-ink-900">{{ $e->description ?? $e->category ?? 'Expense' }}</td>
                                <td class="text-ink-500">{{ \Carbon\Carbon::parse($e->expense_date ?? $e->created_at)->format('M d, Y') }}</td>
                                <td class="text-right font-bold text-red-600">- ₱{{ number_format($e->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-8 text-ink-400">No expenses yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') return;
        
        const peso = '\u20B1';
        const font = { family: 'Manrope', size: 11 };

        // 1. SALES TREND CHART
        const trendEl = document.getElementById('salesChart');
        if (trendEl) {
            const ctx = trendEl.getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 0, 300);
            grad.addColorStop(0, 'rgba(232, 97, 0, 0.25)'); 
            grad.addColorStop(1, 'rgba(232, 97, 0, 0)');

            new Chart(ctx, {
                type: 'line',
                data: { 
                    labels: @json($trend->pluck('label')), 
                    datasets: [{
                        label: 'Sales',
                        data: @json($trend->pluck('sold')), 
                        borderColor: '#E86100', 
                        backgroundColor: grad, 
                        fill: true,
                        borderWidth: 3,
                        tension: 0.4, 
                        pointRadius: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#E86100',
                        pointBorderWidth: 2,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#E86100',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, 
                    animation: { duration: 1500, easing: 'easeOutQuart' },
                    interaction: { mode: 'index', intersect: false },
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1A1715', titleColor: '#A29B90', bodyColor: '#FFFFFF',
                            borderColor: '#3F3A36', borderWidth: 1, padding: 12, cornerRadius: 8, displayColors: false,
                            titleFont: { family: 'Manrope', size: 12, weight: 'bold' },
                            bodyFont: { family: 'Manrope', size: 14, weight: 'bold' },
                            callbacks: { label: function(context) { return peso + context.parsed.y.toLocaleString(); } }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#F0EDE8', borderDash: [4, 4] }, ticks: { color: '#78716A', font } },
                        x: { grid: { display: false }, ticks: { color: '#78716A', font } }
                    }
                }
            });
        }

        // 2. CASH FLOW BREAKDOWN CHART
        const shareEl = document.getElementById('shareChart');
        if (shareEl) {
            const expenses = {{ (float) $chartExpenses }};
            const purchases = {{ (float) $chartPurchases }};
            const profit = {{ (float) $chartProfit }};
            
            const dataVals = [expenses, purchases, profit];
            const isEmpty = dataVals.every(v => v <= 0); 
            
            new Chart(shareEl, {
                type: 'doughnut',
                data: { 
                    labels: ['Expenses', 'Purchases', 'Cash Profit'], 
                    datasets: [{
                        data: isEmpty ? [1] : dataVals,
                        // GINAWANG EMERALD GREEN (#10b981) ANG PROFIT
                        backgroundColor: isEmpty ? ['#EFEBE5'] : ['#ef4444', '#f59e0b', '#10b981'], 
                        borderWidth: 0, 
                        spacing: isEmpty ? 0 : 4, 
                        borderRadius: isEmpty ? 0 : 8, 
                        hoverOffset: 12
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '76%', 
                    animation: { animateScale: true, animateRotate: true, duration: 1200, easing: 'easeOutQuart' },
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1A1715', bodyColor: '#FFFFFF', padding: 10, cornerRadius: 8, displayColors: false,
                            bodyFont: { family: 'Manrope', size: 13, weight: 'bold' },
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + peso + context.parsed.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush