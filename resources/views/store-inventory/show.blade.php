@extends('layouts.admin')
@section('title', 'Store Inventory — show')
@section('content')
<div class="max-w-4xl mx-auto mt-10">
    <div class="card p-12 text-center">
        <div class="w-20 h-20 rounded-full bg-brand-50 text-brand-500 flex items-center justify-center mx-auto mb-4 text-3xl shadow-sm border border-brand-100">
            <i class="fas fa-tools"></i>
        </div>
        <h3 class="text-xl font-bold text-ink-900 mb-2">Store Inventory — show</h3>
        <p class="text-ink-500 max-w-lg mx-auto mb-6">Kini nga module kay na-set up na pero ang UI under construction pa. Sa sunod nga commands, ato i-build ang full functionality.</p>
        <a href="{{ route('inventory.index') }}" class="btn btn-primary">
            Back to Inventory
        </a>
    </div>
</div>
@endsection