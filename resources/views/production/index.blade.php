@extends('layouts.admin')
@section('title', 'Production')
@section('subtitle', 'Manage coffee roasting and processing batches')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200">
        <div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Total Records</div>
        <div class="text-3xl font-black text-espresso-900">0</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200">
        <div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">This Month</div>
        <div class="text-3xl font-black text-espresso-900">&#8369;0.00</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200">
        <div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Pending</div>
        <div class="text-3xl font-black text-espresso-900">0</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-espresso-200">
        <div class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1">Completed</div>
        <div class="text-3xl font-black text-espresso-900">0</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-espresso-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-espresso-200 bg-espresso-50/30 flex justify-between items-center">
        <div>
            <h3 class="font-bold text-espresso-900">Recent Production</h3>
            <p class="text-xs text-espresso-500">Manage and view your records here</p>
        </div>
        <button class="px-4 py-2 bg-espresso-900 text-white rounded-lg font-semibold text-sm shadow-sm opacity-50 cursor-not-allowed" title="Coming Soon">
            <i class="fas fa-plus mr-1"></i> Add New
        </button>
    </div>

    <div class="text-center py-24 bg-white">
        <div class="w-20 h-20 mx-auto bg-espresso-50 rounded-2xl flex items-center justify-center mb-5 border border-espresso-100 shadow-inner">
            <i class="fas fa-industry text-3xl text-espresso-300"></i>
        </div>
        <h3 class="text-xl font-bold text-espresso-800 mb-2">Module In Development</h3>
        <p class="text-sm text-espresso-500 mb-6 max-w-md mx-auto">The <strong>Production</strong> module is currently being built. Please check back soon for full functionality.</p>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-espresso-900 text-white rounded-lg font-semibold hover:bg-espresso-800 transition-all text-sm shadow-md">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection