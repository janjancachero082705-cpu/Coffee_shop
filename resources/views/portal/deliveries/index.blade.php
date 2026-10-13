@extends('portal.layouts.app')

@section('title', 'My Deliveries')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $baseQuery = \App\Models\DeliveryReceipt::where('store_id', $store->id);
    $total = (clone $baseQuery)->count();
    $pending = (clone $baseQuery)->where('status', 'pending')->count();
    $partial = (clone $baseQuery)->where('status', 'partial')->count();
    $paid = (clone $baseQuery)->where('status', 'paid')->count();
@endphp

{{-- HEADER --}}
<div class="pp-head">
    <div class="pp-head-left">
        <div class="pp-title">Deliveries</div>
        <div class="pp-sub">All deliveries to your store</div>
    </div>
</div>

{{-- STATS --}}
<div class="pp-stats">
    <div class="pp-stat">
        <div class="pp-stat-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="pp-stat-value">{{ $total }}</div>
        <div class="pp-stat-label">Total</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon amber">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="pp-stat-value">{{ $pending }}</div>
        <div class="pp-stat-label">Pending</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon blue">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="pp-stat-value">{{ $partial }}</div>
        <div class="pp-stat-label">Partial</div>
    </div>
    <div class="pp-stat">
        <div class="pp-stat-icon green">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <div class="pp-stat-value">{{ $paid }}</div>
        <div class="pp-stat-label">Paid</div>
    </div>
</div>

{{-- LIST --}}
@if($deliveries->isEmpty())
    <div class="pp-empty">
        <div class="pp-empty-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="pp-empty-title">No deliveries yet</div>
        <div class="pp-empty-text">Wala pa'y deliveries sa imong store.</div>
    </div>
@else
    <div class="pp-list pp-anim">
        @foreach($deliveries as $dr)
            @php
                $isConfirmed = $dr->customer_confirmed ?? false;
                $isOut = !$isConfirmed && $dr->out_for_delivery_at;
                $badgeClass = $isConfirmed ? 'paid' : ($isOut ? 'partial' : $dr->status);
                $badgeText = $isConfirmed ? 'Confirmed' : ($isOut ? 'Out for Delivery' : ucfirst($dr->status));
            @endphp
            <a href="{{ route('portal.deliveries.show', $dr->id) }}" class="pp-card dv-card {{ $isConfirmed ? 'is-confirmed' : (($isOut && !$isConfirmed) ? 'has-action' : '') }}">
                <div class="pp-card-head">
                    <div>
                        <div class="pp-card-title">{{ $dr->dr_number }}</div>
                        <div class="pp-card-sub">{{ $dr->items->count() }} item{{ $dr->items->count() != 1 ? 's' : '' }} · {{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}</div>
                    </div>
                    <span class="pp-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                </div>

                @if($isOut && !$isConfirmed)
                    <div class="pp-card-alert">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        Out for Delivery — Tap to confirm
                    </div>
                @endif

                <div class="pp-card-body">
                    <div>
                        <div class="pp-card-amount">₱{{ number_format($dr->total_amount, 2) }}</div>
                        <div class="pp-card-meta">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            {{ \Carbon\Carbon::parse($dr->delivery_date)->diffForHumans() }}
                        </div>
                    </div>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color: #8D6E63;"><path d="M9 18l6-6-6-6"/></svg>
                </div>
            </a>
        @endforeach
    </div>

    @if(method_exists($deliveries, 'links') && $deliveries->hasPages())
        <div style="margin-top: 20px;">{{ $deliveries->withQueryString()->links() }}</div>
    @endif
@endif

@push('styles')
<style>
/* ═══ DELIVERIES INDEX — PREMIUM COFFEE ═══ */
.pp-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
.pp-title { font-size: 24px; font-weight: 800; color: #2D1A11; letter-spacing: -0.03em; margin-bottom: 3px; }
.pp-sub { font-size: 12px; color: #8D6E63; font-weight: 500; }

.pp-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 18px; }
.pp-stat { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 16px 8px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 16px; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.pp-stat-icon { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; background: #EFEBE9; color: #2D1A11; }
.pp-stat-icon.amber { background: #FEF3C7; color: #B45309; }
.pp-stat-icon.blue  { background: #DBEAFE; color: #2563EB; }
.pp-stat-icon.green { background: #DCFCE7; color: #15803D; }
.pp-stat-value { font-size: 20px; font-weight: 800; color: #2D1A11; font-variant-numeric: tabular-nums; line-height: 1; }
.pp-stat-label { font-size: 10px; font-weight: 800; color: #8D6E63; text-transform: uppercase; letter-spacing: 0.08em; }

.pp-list { display: flex; flex-direction: column; gap: 12px; }
.pp-card { display: block; padding: 18px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 18px; text-decoration: none; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.pp-card:hover { border-color: #D7CCC8; transform: translateY(-3px); box-shadow: 0 12px 28px -12px rgba(45, 26, 17, 0.15); }
.pp-card:active { transform: scale(0.99); }
.pp-card.has-action { border-color: #BFDBFE; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.08); }

.pp-card-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
.pp-card-title { font-size: 15px; font-weight: 800; color: #2D1A11; font-family: ui-monospace, monospace; margin-bottom: 4px; }
.pp-card-sub { font-size: 11.5px; color: #8D6E63; font-weight: 600; }

.pp-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 100px; font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; border: 1px solid; flex-shrink: 0; }
.pp-badge.pending  { background: #FEF3C7; color: #B45309; border-color: #FDE68A; }
.pp-badge.partial  { background: #DBEAFE; color: #1D4ED8; border-color: #BFDBFE; }
.pp-badge.paid, .pp-badge.confirmed { background: #DCFCE7; color: #15803D; border-color: #BBF7D0; }
.pp-badge.cancelled { background: #FEE2E2; color: #B91C1C; border-color: #FECACA; }

.pp-card-alert { display: flex; align-items: center; gap: 8px; padding: 10px 12px; margin: 0 -18px 12px; padding-left: 18px; padding-right: 18px; background: #EFF6FF; border-top: 1px solid #BFDBFE; border-bottom: 1px solid #BFDBFE; color: #1D4ED8; font-size: 11.5px; font-weight: 700; }
.pp-card-alert svg { flex-shrink: 0; animation: truckMove 1.5s ease-in-out infinite; }
@keyframes truckMove { 0%, 100% { transform: translateX(0); } 50% { transform: translateX(3px); } }

.pp-card-body { display: flex; justify-content: space-between; align-items: flex-end; gap: 12px; padding-top: 12px; border-top: 1.5px solid #EFEBE9; }
.pp-card-amount { font-size: 20px; font-weight: 800; color: #D84315; letter-spacing: -0.02em; font-variant-numeric: tabular-nums; }
.pp-card-meta { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; color: #8D6E63; font-weight: 600; margin-top: 4px; }

.pp-empty { text-align: center; padding: 60px 20px; background: #FFFFFF; border: 1.5px dashed #D7CCC8; border-radius: 18px; }
.pp-empty-icon { width: 72px; height: 72px; margin: 0 auto 16px; border-radius: 20px; background: #FDFBF7; border: 1.5px solid #EFEBE9; color: #D7CCC8; display: grid; place-items: center; }
.pp-empty-title { font-size: 17px; font-weight: 800; color: #2D1A11; margin-bottom: 6px; }
.pp-empty-text { font-size: 12.5px; color: #8D6E63; }

@media (max-width: 480px) {
    .pp-title { font-size: 20px; }
    .pp-stats { gap: 6px; }
    .pp-stat { padding: 12px 4px; }
    .pp-stat-icon { width: 34px; height: 34px; }
    .pp-stat-value { font-size: 17px; }
    .pp-stat-label { font-size: 9px; }
    .pp-card { padding: 14px; }
    .pp-card-alert { margin-left: -14px; margin-right: -14px; padding-left: 14px; padding-right: 14px; }
}
@media (max-width: 380px) {
    .pp-stats { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

@endsection