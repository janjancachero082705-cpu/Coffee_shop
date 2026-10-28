@extends('layouts.admin')
@section('title', $purchase->purchase_number)
@section('subtitle', $purchase->supplier_name)
@section('content')
<style>
    /* ==========================================
       PURCHASE HISTORY MOBILE RESPONSIVE FIX
       ========================================== */
    @media (max-width: 767px) {
        /* 1. Siguraduhon nga mo-scroll ang table sa kilid */
        .history-table-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0.75rem;
            border: 1px solid #E8E5E0;
        }
        
        /* 2. Pugngan ang table sa pag-squish */
        .history-table-wrapper table {
            min-width: 700px;
            width: 100%;
        }

        /* 3. I-adjust ang mga text ug badges sa mobile */
        .history-table-wrapper th, 
        .history-table-wrapper td {
            font-size: 11px !important;
            padding: 0.75rem 0.5rem !important;
            white-space: nowrap;
        }

        /* 4. Ipa-stack ang mga header controls kung naay filter/search */
        .history-controls {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
        }

        /* 5. Ipa-full width ang search bar ug buttons */
        .history-controls input,
        .history-controls select,
        .history-controls button {
            width: 100% !important;
        }
    }
</style>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
    <div class="lg:col-span-2 card p-6">
        <h2 class="font-black text-xl text-ink-900 mb-4">{{ $purchase->purchase_number }}</h2>
        <div class="grid grid-cols-2 gap-4 text-sm border-t border-ink-200 pt-4">
            <div><div class="text-[10px] text-ink-500 uppercase font-bold tracking-wider">Supplier</div><div class="font-semibold text-ink-900">{{ $purchase->supplier_name }}</div></div>
            <div><div class="text-[10px] text-ink-500 uppercase font-bold tracking-wider">Date</div><div class="font-semibold text-ink-900">{{ $purchase->purchase_date->format('M d, Y') }}</div></div>
            <div><div class="text-[10px] text-ink-500 uppercase font-bold tracking-wider">Quantity</div><div class="font-semibold text-ink-900">{{ $purchase->quantity }} {{ $purchase->unit }}</div></div>
            <div><div class="text-[10px] text-ink-500 uppercase font-bold tracking-wider">Unit Price</div><div class="font-semibold text-ink-900">&#8369;{{ number_format($purchase->unit_price,2) }}</div></div>
            <div><div class="text-[10px] text-ink-500 uppercase font-bold tracking-wider">Total</div><div class="text-xl font-black text-brand-600">&#8369;{{ number_format($purchase->total_amount,2) }}</div></div>
            <div><div class="text-[10px] text-ink-500 uppercase font-bold tracking-wider">Paid</div><div class="font-semibold text-emerald-600">&#8369;{{ number_format($purchase->amount_paid,2) }}</div></div>
        </div>
    </div>
    <div class="card p-5 border-l-4 border-red-500">
        <div class="text-[10px] font-bold text-ink-500 uppercase tracking-wider mb-1">Balance</div>
        <div class="text-2xl font-black text-red-600">&#8369;{{ number_format($purchase->total_amount - $purchase->amount_paid, 2) }}</div>
    </div>
</div>
@endsection