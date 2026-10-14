@extends('portal.layouts.app')

@section('title', 'Sales Reports')

@section('content')

@php
    $totalPaid = $reports->sum('amount_paid');
    $totalBalance = $reports->sum('balance');
    $totalDue = $reports->sum('amount_due');
    $paidPercent = $totalDue > 0 ? round(($totalPaid / $totalDue) * 100) : 0;
@endphp

{{-- HERO --}}
<div class="rp-hero">
    <div class="rp-hero-glow"></div>
    <div class="rp-hero-content">
        <div class="rp-hero-label">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/></svg>
            Sales Reports
        </div>
        <div class="rp-hero-value">{{ $stats['total'] }}</div>
        <div class="rp-hero-meta">
            <span class="rp-hero-dot" style="background:#4ADE80;"></span>
            ₱{{ number_format($totalPaid, 0) }} paid
            <span style="opacity:.4;">·</span>
            <span class="rp-hero-dot" style="background:#FBBF24;"></span>
            ₱{{ number_format($totalBalance, 0) }} balance
        </div>
    </div>
    <div class="rp-hero-ring">
        <svg viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(255,255,255,.12)" stroke-width="8"/>
            <circle cx="50" cy="50" r="42" fill="none" stroke="#E8A33D" stroke-width="8" stroke-linecap="round"
                    stroke-dasharray="264" stroke-dashoffset="{{ 264 - (264 * $paidPercent / 100) }}"
                    transform="rotate(-90 50 50)"/>
        </svg>
        <div class="rp-hero-ring-text">
            <div class="rp-hero-ring-num">{{ $paidPercent }}%</div>
            <div class="rp-hero-ring-lbl">Paid</div>
        </div>
    </div>
</div>

{{-- QUICK CHIPS --}}
<div class="rp-quick">
    <div class="rp-quick-chip">
        <div class="rp-quick-icon" style="--c:#15803D;--bg:#E8F5E9;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <div>
            <div class="rp-quick-value">₱{{ number_format($totalPaid, 0) }}</div>
            <div class="rp-quick-label">Paid</div>
        </div>
    </div>
    <div class="rp-quick-chip">
        <div class="rp-quick-icon" style="--c:#B45309;--bg:#FFF8E1;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div>
            <div class="rp-quick-value">₱{{ number_format($totalBalance, 0) }}</div>
            <div class="rp-quick-label">Balance</div>
        </div>
    </div>
</div>

{{-- SECTION LABEL --}}
<div class="rp-section-label">
    <span>Recent Reports</span>
    <span class="rp-section-count">{{ $stats['total'] }}</span>
</div>

{{-- LIST --}}
@if($reports->isEmpty())
    <div class="rp-empty">
        <div class="rp-empty-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/></svg>
        </div>
        <div class="rp-empty-title">Wala pay reports</div>
        <div class="rp-empty-text">Wala pa kay sales report.</div>
    </div>
@else
    <div class="rp-list">
        @foreach($reports as $r)
            @php
                $paidPercent = $r->amount_due > 0 ? min(100, ($r->amount_paid / $r->amount_due) * 100) : 0;
                $statusKey = in_array($r->status, ['paid', 'partial', 'verified']) ? $r->status : 'pending';
                $statusText = ucfirst($r->status);
            @endphp
            <a href="{{ route('portal.reports.show', $r->id) }}" class="rp-card">
                <div class="rp-card-stripe" data-status="{{ $statusKey }}"></div>

                <div class="rp-card-head">
                    <div class="rp-card-num">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/></svg>
                        {{ $r->report_number }}
                    </div>
                    <span class="rp-badge rp-badge-{{ $statusKey }}">{{ $statusText }}</span>
                </div>

                <div class="rp-card-meta">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ \Carbon\Carbon::parse($r->period_from)->format('M d') }} – {{ \Carbon\Carbon::parse($r->period_to)->format('M d, Y') }}
                    @if($r->deliveryReceipt)
                        <span style="opacity:.4;">·</span>
                        {{ $r->deliveryReceipt->dr_number }}
                    @endif
                </div>

                <div class="rp-progress-wrap">
                    <div class="rp-progress-info">
                        <span>₱{{ number_format($r->amount_paid, 0) }} paid</span>
                        <strong>{{ number_format($paidPercent, 0) }}%</strong>
                    </div>
                    <div class="rp-progress-bar">
                        <div class="rp-progress-fill" style="width: {{ $paidPercent }}%"></div>
                    </div>
                </div>

                <div class="rp-card-amount">
                    <div>
                        <div class="rp-card-amount-lbl">Amount Due</div>
                        <div class="rp-card-amount-val">₱{{ number_format($r->amount_due, 2) }}</div>
                    </div>
                    <div class="rp-card-arrow">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    @if(method_exists($reports, 'links') && $reports->hasPages())
        <div style="margin-top:20px;">{{ $reports->withQueryString()->links() }}</div>
    @endif
@endif

@endsection

@push('styles')
<style>
/* ═══ REPORTS INDEX — MATCHING APP DESIGN ═══ */

/* HERO */
.rp-hero{position:relative;display:flex;align-items:center;justify-content:space-between;gap:14px;padding:20px;background:linear-gradient(135deg,#1A1A1A 0%,#2D2D2D 55%,#3D3D3D 100%);border-radius:18px;margin-bottom:14px;overflow:hidden;animation:fadeIn .35s ease both}
.rp-hero-glow{position:absolute;top:-80px;right:-80px;width:240px;height:240px;background:radial-gradient(circle,rgba(232,163,61,.4),transparent 70%);pointer-events:none;animation:heroGlow 5s ease-in-out infinite}
@keyframes heroGlow{0%,100%{opacity:.7;transform:scale(1);}50%{opacity:1;transform:scale(1.15);}}
.rp-hero-content{position:relative;z-index:1;flex:1;min-width:0}
.rp-hero-label{display:inline-flex;align-items:center;gap:6px;font-size:10.5px;font-weight:800;color:rgba(255,255,255,.65);text-transform:uppercase;letter-spacing:.12em;margin-bottom:8px}
.rp-hero-label svg{color:#E8A33D}
.rp-hero-value{font-size:44px;font-weight:800;color:#FFFFFF;letter-spacing:-.045em;line-height:1;margin-bottom:12px;font-variant-numeric:tabular-nums}
.rp-hero-meta{display:flex;align-items:center;gap:6px;font-size:11.5px;color:rgba(255,255,255,.7);font-weight:600}
.rp-hero-dot{display:inline-block;width:6px;height:6px;border-radius:50%;box-shadow:0 0 6px currentColor}
.rp-hero-ring{position:relative;width:108px;height:108px;flex-shrink:0;z-index:1}
.rp-hero-ring svg{width:100%;height:100%;display:block}
.rp-hero-ring-text{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center}
.rp-hero-ring-num{font-size:22px;font-weight:800;color:#FFFFFF;letter-spacing:-.02em;line-height:1;font-variant-numeric:tabular-nums}
.rp-hero-ring-lbl{font-size:9px;font-weight:700;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:.1em;margin-top:4px}

/* QUICK CHIPS */
.rp-quick{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px;animation:fadeIn .35s ease both;animation-delay:.05s}
.rp-quick-chip{display:flex;align-items:center;gap:10px;padding:12px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;transition:all .15s}
.rp-quick-chip:hover{border-color:#D0D0D0;transform:translateY(-2px);box-shadow:0 6px 14px -6px rgba(0,0,0,.08)}
.rp-quick-icon{width:34px;height:34px;border-radius:9px;background:var(--bg,#F5F5F5);color:var(--c,#757575);display:grid;place-items:center;flex-shrink:0}
.rp-quick-value{font-size:15px;font-weight:800;color:#1A1A1A;letter-spacing:-.02em;line-height:1.1;font-variant-numeric:tabular-nums}
.rp-quick-label{font-size:9.5px;font-weight:700;color:#757575;text-transform:uppercase;letter-spacing:.08em;margin-top:1px}

/* SECTION LABEL */
.rp-section-label{display:flex;justify-content:space-between;align-items:center;margin:0 4px 12px}
.rp-section-label span:first-child{font-size:12px;font-weight:800;color:#1A1A1A;text-transform:uppercase;letter-spacing:.08em}
.rp-section-count{font-size:11px;font-weight:700;color:#757575;background:#F5F5F5;padding:3px 10px;border-radius:100px}

/* LIST */
.rp-list{display:flex;flex-direction:column;gap:10px;animation:fadeIn .4s ease both;animation-delay:.1s}

/* CARD */
.rp-card{position:relative;display:block;padding:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:14px;text-decoration:none;color:inherit;transition:all .2s cubic-bezier(.4,0,.2,1);overflow:hidden}
.rp-card:hover{transform:translateY(-2px);box-shadow:0 12px 24px -10px rgba(0,0,0,.1);border-color:transparent}
.rp-card:active{transform:scale(.99)}

/* STRIPE */
.rp-card-stripe{position:absolute;top:0;left:0;right:0;height:3px;border-radius:14px 14px 0 0}
.rp-card-stripe[data-status="paid"]{background:linear-gradient(90deg,#15803D,#4ADE80)}
.rp-card-stripe[data-status="partial"]{background:linear-gradient(90deg,#F59E0B,#FBBF24)}
.rp-card-stripe[data-status="verified"]{background:linear-gradient(90deg,#2563EB,#60A5FA)}
.rp-card-stripe[data-status="pending"]{background:linear-gradient(90deg,#9E9E9E,#BDBDBD)}

/* HEAD */
.rp-card-head{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:8px;margin-top:4px}
.rp-card-num{display:inline-flex;align-items:center;gap:5px;font-size:12.5px;font-weight:800;color:#1A1A1A;font-family:ui-monospace,monospace;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;min-width:0;letter-spacing:-.01em}
.rp-card-num svg{color:#9E9E9E;flex-shrink:0}
.rp-badge{display:inline-flex;padding:3px 9px;border-radius:100px;font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;flex-shrink:0}
.rp-badge-paid{background:#E8F5E9;color:#15803D}
.rp-badge-partial{background:#FFF8E1;color:#B45309}
.rp-badge-verified{background:#EFF6FF;color:#1D4ED8}
.rp-badge-pending{background:#F5F5F5;color:#757575}

/* META */
.rp-card-meta{display:flex;align-items:center;gap:5px;font-size:10.5px;color:#757575;font-weight:600;margin-bottom:10px;flex-wrap:wrap}
.rp-card-meta svg{color:#9E9E9E;flex-shrink:0}

/* PROGRESS */
.rp-progress-wrap{margin-bottom:12px}
.rp-progress-info{display:flex;justify-content:space-between;align-items:center;font-size:10.5px;color:#757575;font-weight:600;margin-bottom:5px}
.rp-progress-info strong{color:#1A1A1A;font-weight:800;font-variant-numeric:tabular-nums}
.rp-progress-bar{width:100%;height:6px;background:#F5F5F5;border-radius:100px;overflow:hidden}
.rp-progress-fill{height:100%;background:linear-gradient(90deg,#E8A33D,#F0B85A);border-radius:100px;transition:width .6s cubic-bezier(.4,0,.2,1);min-width:4px;box-shadow:0 0 8px rgba(232,163,61,.4)}

/* AMOUNT */
.rp-card-amount{display:flex;justify-content:space-between;align-items:flex-end;gap:12px;padding-top:10px;border-top:1px dashed #F0F0F0}
.rp-card-amount-lbl{font-size:9.5px;font-weight:800;color:#757575;text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px}
.rp-card-amount-val{font-size:18px;font-weight:800;color:#D4901F;letter-spacing:-.03em;font-variant-numeric:tabular-nums;line-height:1}
.rp-card-arrow{color:#C0C0C0;transition:all .2s;display:grid;place-items:center}
.rp-card:hover .rp-card-arrow{color:#D4901F;transform:translateX(3px)}

/* EMPTY */
.rp-empty{text-align:center;padding:48px 20px;background:#FFFFFF;border:1px dashed #E0E0E0;border-radius:14px}
.rp-empty-icon{width:64px;height:64px;margin:0 auto 14px;border-radius:16px;background:#FFF8EC;border:1px solid #F0E0C0;color:#D4901F;display:grid;place-items:center}
.rp-empty-title{font-size:15px;font-weight:800;color:#1A1A1A;margin-bottom:5px}
.rp-empty-text{font-size:12.5px;color:#757575;font-weight:500}

@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

@media(max-width:480px){
.rp-hero{padding:18px}
.rp-hero-value{font-size:38px}
.rp-hero-ring{width:92px;height:92px}
.rp-hero-ring-num{font-size:19px}
.rp-card{padding:12px}
.rp-card-amount-val{font-size:16px}
}
</style>
@endpush
