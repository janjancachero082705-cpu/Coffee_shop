@extends('layouts.admin')
@section('title', 'Store Stocks')
@section('subtitle', 'Monitor inventory levels per store')

@section('content')
<div class="w-full max-w-screen-2xl mx-auto space-y-6">
    
    {{-- KPI BOXES --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="card p-5">
            <div class="text-[11px] font-bold text-ink-500 uppercase tracking-wider mb-1">Total Records</div>
            <div class="text-2xl font-black text-ink-900">0</div>
        </div>
        <div class="card p-5">
            <div class="text-[11px] font-bold text-ink-500 uppercase tracking-wider mb-1">This Month</div>
            <div class="text-2xl font-black text-ink-900">&#8369;0.00</div>
        </div>
        <div class="card p-5">
            <div class="text-[11px] font-bold text-ink-500 uppercase tracking-wider mb-1">Pending</div>
            <div class="text-2xl font-black text-ink-900">0</div>
        </div>
        <div class="card p-5">
            <div class="text-[11px] font-bold text-ink-500 uppercase tracking-wider mb-1">Completed</div>
            <div class="text-2xl font-black text-ink-900">0</div>
        </div>
    </div>

    {{-- MAIN CARD --}}
    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-ink-200 flex justify-between items-center bg-ink-50">
            <div>
                <h3 class="font-bold text-ink-800">Recent Store Stocks</h3>
                <p class="text-xs text-ink-500">Manage and view your records here</p>
            </div>
            <button class="btn btn-primary opacity-50 cursor-not-allowed" title="Coming Soon">
                <i class="fas fa-plus"></i> Add New
            </button>
        </div>

        <div class="text-center py-24 bg-white">
            <div class="w-20 h-20 mx-auto bg-ink-50 rounded-2xl flex items-center justify-center mb-5 border border-ink-200 shadow-inner">
                <i class="fas fa-clipboard-list text-3xl text-ink-300"></i>
            </div>
            <h3 class="text-xl font-bold text-ink-900 mb-2">Module In Development</h3>
            <p class="text-sm text-ink-500 mb-6 max-w-md mx-auto">The <strong>Store Stocks</strong> module is currently being built. Please check back soon for full functionality.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary mx-auto">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection