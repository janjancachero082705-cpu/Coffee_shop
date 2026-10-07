@extends('portal.layouts.app')

@section('title', 'Payments')

@section('content')

<div class="pp-head" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div class="pp-head-left">
        <div class="pp-title">Payments</div>
        <div class="pp-sub">Imong payment history</div>
    </div>
    <a href="{{ route('portal.payments.create') }}" class="pp-pay-btn">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14"/>
        </svg>
        Record
    </a>
</div>

{{-- STATS --}}
<div class="pp-stats">
    <div class="pp-stat">
        <div class="pp-stat-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
        <div class="pp-stat-value">{{ $stats['total'] }}</div>
        <div class="pp-stat-label">Payments</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon green"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div>
        <div class="pp-stat-value">&#8369;{{ number_format($stats['all_amount'], 0) }}</div>
        <div class="pp-stat-label">Total Paid</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon amber"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
        <div class="pp-stat-value">&#8369;{{ number_format($stats['this_month'], 0) }}</div>
        <div class="pp-stat-label">This Month</div>
    </div>
</div>

@if($payments->isEmpty())
    <div class="pp-empty">
        <div class="pp-empty-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <rect x="1" y="4" width="22" height="16" rx="2"/>
                <path d="M1 10h22"/>
            </svg>
        </div>
        <div class="pp-empty-title">No payments yet</div>
        <div class="pp-empty-text">Wala pa kay na-record nga payment.</div>
        <a href="{{ route('portal.payments.create') }}" class="pp-empty-btn">Record your first payment</a>
    </div>
@else
    <div class="pp-list pp-anim">
        @foreach($payments as $p)
            <a href="{{ route('portal.payments.show', $p->id) }}" class="pp-card">
                <div class="pp-card-head">
                    <div>
                        <div class="pp-card-title">{{ $p->payment_number }}</div>
                        <div class="pp-card-sub">
                            @if($p->deliveryReceipt)
                                {{ $p->deliveryReceipt->dr_number }} ·
                            @endif
                            {{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}
                        </div>
                    </div>
                    <span class="pp-method-badge" style="--mc: {{ $p->method_color }};">
                        <span>{{ $p->method_icon }}</span>
                        {{ $p->method_label }}
                    </span>
                </div>
                <div class="pp-card-body">
                    <div>
                        <div class="pp-card-amount">&#8369;{{ number_format($p->amount, 2) }}</div>
                        @if($p->reference_number)
                            <div class="pp-card-meta">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 7h16M4 12h10M4 17h16"/>
                                </svg>
                                Ref: {{ $p->reference_number }}
                            </div>
                        @endif
                    </div>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color:var(--text-muted);">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>

    @if(method_exists($payments, 'links') && $payments->hasPages())
        <div style="margin-top:20px;">{{ $payments->withQueryString()->links() }}</div>
    @endif
@endif

@endsection

@push('styles')
<style>
    .pp-pay-btn {
        display:inline-flex; align-items:center; gap:6px;
        padding:9px 16px;
        background:linear-gradient(135deg,#c9a961,#8a5f36);
        color:#fff; text-decoration:none;
        border-radius:11px;
        font-size:12.5px; font-weight:800;
        box-shadow:0 8px 20px -8px rgba(201,169,97,0.7);
    }
    .pp-pay-btn:hover { background:linear-gradient(135deg,#d4b673,#9a6b3f); }
    .pp-pay-btn:active { transform:scale(0.97); }

    .pp-method-badge {
        display:inline-flex; align-items:center; gap:5px;
        padding:5px 10px;
        background:color-mix(in srgb, var(--mc) 15%, transparent);
        border:1px solid color-mix(in srgb, var(--mc) 40%, transparent);
        color:var(--mc);
        border-radius:8px;
        font-size:11px; font-weight:800;
        letter-spacing:0.01em;
        flex-shrink:0;
    }

    .pp-empty {
        text-align:center; padding:44px 20px;
        background:linear-gradient(165deg,#1e1a16,#15120f);
        border:1px dashed rgba(255,255,255,0.08);
        border-radius:18px;
    }
    .pp-empty-icon {
        width:64px; height:64px; margin:0 auto 14px;
        border-radius:20px;
        background:rgba(201,169,97,0.1);
        color:#c9a961;
        display:grid; place-items:center;
    }
    .pp-empty-title { font-size:15px; font-weight:800; color:#f5f3f0; margin-bottom:6px; }
    .pp-empty-text { font-size:12.5px; color:#8a8378; margin-bottom:16px; }
    .pp-empty-btn {
        display:inline-flex; align-items:center; gap:6px;
        padding:11px 20px;
        background:linear-gradient(135deg,#c9a961,#8a5f36);
        color:#fff; text-decoration:none;
        border-radius:11px;
        font-size:12.5px; font-weight:800;
    }
</style>
@endpush