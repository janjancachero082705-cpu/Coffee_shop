@extends('portal.layouts.app')

@section('title', $payment->payment_number)

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('portal.payments.index') }}" class="back-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <div class="page-title">{{ $payment->payment_number }}</div>
            <div class="page-sub">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</div>
        </div>
    </div>
</div>

<div class="hero">
    <div class="hero-content">
        <div class="hero-label">Amount Paid</div>
        <div class="hero-value green">&#8369;{{ number_format($payment->amount, 2) }}</div>
        <div class="hero-meta">
            <div class="hero-meta-item">
                <span class="hero-meta-dot green"></span>
                Received via {{ ucfirst(str_replace('_', ' ', $payment->method)) }}
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <div class="card-head-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
        </div>
        <div>
            <div class="card-head-title">Payment Details</div>
        </div>
    </div>

    <div class="info-list">
        <div class="info-row">
            <span class="info-label">Payment #</span>
            <span class="info-value" style="font-family:ui-monospace;">{{ $payment->payment_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Date</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Method</span>
            <span class="info-value">{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</span>
        </div>
        @if($payment->reference_number)
            <div class="info-row">
                <span class="info-label">Reference</span>
                <span class="info-value" style="font-family:ui-monospace;">{{ $payment->reference_number }}</span>
            </div>
        @endif
        @if($payment->notes)
            <div class="info-row">
                <span class="info-label">Notes</span>
                <span class="info-value">{{ $payment->notes }}</span>
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    .info-list { display: flex; flex-direction: column; }
    .info-row { display: flex; justify-content: space-between; gap: 16px; padding: 12px 0; border-bottom: 1px solid var(--border); font-size: 13px; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: var(--text-muted); font-weight: 600; }
    .info-value { color: var(--text-primary); text-align: right; font-weight: 500; }
</style>
@endpush