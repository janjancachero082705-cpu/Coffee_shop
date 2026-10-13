@extends('portal.layouts.app')

@section('title', 'Sales Reports')

@section('content')

<div class="rp-head">
    <div class="rp-title">Sales Reports</div>
    <div class="rp-sub">Imong sales ug payment status</div>
</div>

@php
    $totalPaid = $reports->sum('amount_paid');
    $totalBalance = $reports->sum('balance');
@endphp

<div class="rp-stats">
    <div class="rp-stat">
        <div class="rp-stat-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></svg></div>
        <div class="rp-stat-value">{{ $stats['total'] }}</div>
        <div class="rp-stat-label">Reports</div>
    </div>
    <div class="rp-stat">
        <div class="rp-stat-icon green"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div>
        <div class="rp-stat-value">₱{{ number_format($totalPaid, 0) }}</div>
        <div class="rp-stat-label">Total Paid</div>
    </div>
    <div class="rp-stat">
        <div class="rp-stat-icon amber"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
        <div class="rp-stat-value">₱{{ number_format($totalBalance, 0) }}</div>
        <div class="rp-stat-label">Balance</div>
    </div>
</div>

@if($reports->isEmpty())
    <div class="rp-empty">
        <div class="rp-empty-icon"><svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></svg></div>
        <div class="rp-empty-title">Wala pay reports</div>
        <div class="rp-empty-text">Wala pa kay sales report.</div>
    </div>
@else
    <div class="rp-list">
        @foreach($reports as $r)
            @php
                $paidPercent = $r->amount_due > 0 ? min(100, ($r->amount_paid / $r->amount_due) * 100) : 0;
                $statusColor = match($r->status) {
                    'paid' => '#16A34A',
                    'partial' => '#B45309',
                    'verified' => '#2563EB',
                    default => '#8D6E63',
                };
            @endphp
            <a href="{{ route('portal.reports.show', $r->id) }}" class="rp-card">
                <div class="rp-card-head">
                    <div>
                        <div class="rp-card-title">{{ $r->report_number }}</div>
                        <div class="rp-card-sub">{{ \Carbon\Carbon::parse($r->period_from)->format('M d') }} - {{ \Carbon\Carbon::parse($r->period_to)->format('M d, Y') }}@if($r->deliveryReceipt) · {{ $r->deliveryReceipt->dr_number }}@endif</div>
                    </div>
                    <span class="rp-badge" style="background:{{ $statusColor }}22;color:{{ $statusColor }};border:1px solid {{ $statusColor }}55;">{{ ucfirst($r->status) }}</span>
                </div>
                <div class="rp-card-body">
                    <div style="flex:1;">
                        <div class="rp-card-amount">₱{{ number_format($r->amount_due, 2) }}</div>
                        <div class="rp-progress"><div class="rp-progress-bar" style="width:{{ $paidPercent }}%;background:{{ $statusColor }};"></div></div>
                        <div class="rp-meta-row"><span>Paid: ₱{{ number_format($r->amount_paid, 2) }}</span><span>Bal: ₱{{ number_format($r->balance, 2) }}</span></div>
                    </div>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="rp-arrow"><path d="M9 18l6-6-6-6"/></svg>
                </div>
            </a>
        @endforeach
    </div>
    @if(method_exists($reports, 'links') && $reports->hasPages())<div style="margin-top:20px;">{{ $reports->withQueryString()->links() }}</div>@endif
@endif

@endsection

@push('styles')
<style>
/* ═══ REPORTS INDEX — PREMIUM COFFEE ═══ */
.rp-head { margin-bottom: 18px; }
.rp-title { font-size: 24px; font-weight: 800; color: #2D1A11; letter-spacing: -0.03em; margin-bottom: 3px; }
.rp-sub { font-size: 12px; color: #8D6E63; font-weight: 500; }

.rp-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 18px; }
.rp-stat { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 16px 8px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 16px; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.rp-stat-icon { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; background: #EFEBE9; color: #2D1A11; }
.rp-stat-icon.green { background: #DCFCE7; color: #15803D; }
.rp-stat-icon.amber { background: #FEF3C7; color: #B45309; }
.rp-stat-value { font-size: 18px; font-weight: 800; color: #2D1A11; font-variant-numeric: tabular-nums; line-height: 1; }
.rp-stat-label { font-size: 10px; font-weight: 800; color: #8D6E63; text-transform: uppercase; letter-spacing: 0.08em; }

.rp-list { display: flex; flex-direction: column; gap: 12px; }
.rp-card { display: block; padding: 18px; background: #FFFFFF; border: 1.5px solid #EFEBE9; border-radius: 18px; text-decoration: none; color: inherit; transition: all 0.25s; box-shadow: 0 2px 8px rgba(45, 26, 17, 0.04); }
.rp-card:hover { border-color: #D7CCC8; transform: translateY(-3px); box-shadow: 0 12px 28px -12px rgba(45, 26, 17, 0.15); }
.rp-card-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
.rp-card-title { font-size: 14px; font-weight: 800; color: #2D1A11; font-family: ui-monospace, monospace; margin-bottom: 3px; }
.rp-card-sub { font-size: 11px; color: #8D6E63; font-weight: 600; }
.rp-badge { padding: 4px 9px; border-radius: 7px; font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; }
.rp-card-body { display: flex; align-items: center; gap: 12px; }
.rp-card-amount { font-size: 18px; font-weight: 800; color: #D84315; font-variant-numeric: tabular-nums; margin-bottom: 8px; }
.rp-progress { width: 100%; height: 6px; background: #EFEBE9; border-radius: 100px; overflow: hidden; margin-bottom: 6px; }
.rp-progress-bar { height: 100%; border-radius: 100px; transition: width 0.4s; }
.rp-meta-row { display: flex; justify-content: space-between; font-size: 10.5px; color: #8D6E63; font-weight: 600; }
.rp-arrow { color: #D7CCC8; flex-shrink: 0; }

.rp-empty { text-align: center; padding: 60px 20px; background: #FFFFFF; border: 1.5px dashed #D7CCC8; border-radius: 18px; }
.rp-empty-icon { width: 72px; height: 72px; margin: 0 auto 16px; border-radius: 20px; background: #FDFBF7; border: 1.5px solid #EFEBE9; color: #D7CCC8; display: grid; place-items: center; }
.rp-empty-title { font-size: 17px; font-weight: 800; color: #2D1A11; margin-bottom: 6px; }
.rp-empty-text { font-size: 12.5px; color: #8D6E63; }

@media (max-width: 480px) {
    .rp-title { font-size: 20px; }
    .rp-stats { gap: 6px; }
    .rp-stat { padding: 12px 4px; }
    .rp-stat-icon { width: 34px; height: 34px; }
    .rp-stat-value { font-size: 15px; }
    .rp-stat-label { font-size: 9px; }
    .rp-card { padding: 14px; }
}
</style>
@endpush