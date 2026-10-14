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
    $totalAmount = (clone $baseQuery)->sum('total_amount');
    $paidAmount = (clone $baseQuery)->where('status', 'paid')->sum('total_amount');
    $paidPercent = $totalAmount > 0 ? round(($paidAmount / $totalAmount) * 100) : 0;
@endphp

{{-- HERO CARD --}}
<div class="dv-hero">
    <div class="dv-hero-glow"></div>
    <div class="dv-hero-pattern"></div>

    <div class="dv-hero-content">
        <div class="dv-hero-label">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            Total Deliveries
        </div>
        <div class="dv-hero-value">{{ $total }}</div>
        <div class="dv-hero-meta">
            <span class="dv-hero-dot" style="background:#4ADE80;"></span>
            {{ $paid }} paid
            <span style="opacity:.4;">·</span>
            <span class="dv-hero-dot" style="background:#FBBF24;"></span>
            {{ $pending }} pending
        </div>
    </div>

    <div class="dv-hero-ring">
        <svg viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(255,255,255,.12)" stroke-width="8"/>
            <circle cx="50" cy="50" r="42" fill="none" stroke="#E8A33D" stroke-width="8" stroke-linecap="round"
                    stroke-dasharray="264" stroke-dashoffset="{{ 264 - (264 * $paidPercent / 100) }}"
                    transform="rotate(-90 50 50)"/>
        </svg>
        <div class="dv-hero-ring-text">
            <div class="dv-hero-ring-num">{{ $paidPercent }}%</div>
            <div class="dv-hero-ring-lbl">Paid</div>
        </div>
    </div>
</div>

{{-- QUICK STATS — horizontal scroll --}}
<div class="dv-quick">
    <div class="dv-quick-chip">
        <div class="dv-quick-icon" style="--c:#B45309;--bg:#FFF8E1;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div>
            <div class="dv-quick-value">{{ $pending }}</div>
            <div class="dv-quick-label">Pending</div>
        </div>
    </div>
    <div class="dv-quick-chip">
        <div class="dv-quick-icon" style="--c:#1D4ED8;--bg:#EFF6FF;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div>
            <div class="dv-quick-value">{{ $partial }}</div>
            <div class="dv-quick-label">Partial</div>
        </div>
    </div>
    <div class="dv-quick-chip">
        <div class="dv-quick-icon" style="--c:#15803D;--bg:#E8F5E9;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <div>
            <div class="dv-quick-value">{{ $paid }}</div>
            <div class="dv-quick-label">Paid</div>
        </div>
    </div>
    <div class="dv-quick-chip">
        <div class="dv-quick-icon" style="--c:#D4901F;--bg:#FFF8EC;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div>
            <div class="dv-quick-value">₱{{ number_format($totalAmount/1000, 1) }}k</div>
            <div class="dv-quick-label">Total</div>
        </div>
    </div>
</div>

{{-- SECTION LABEL --}}
<div class="dv-section-label">
    <span>Recent Deliveries</span>
    <span class="dv-section-count">{{ $total }}</span>
</div>

{{-- TIMELINE-STYLE LIST --}}
@if($deliveries->isEmpty())
    <div class="dv-empty">
        <div class="dv-empty-icon">
            <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="dv-empty-title">No deliveries yet</div>
        <div class="dv-empty-text">Wala pa'y deliveries sa imong store.</div>
    </div>
@else
    <div class="dv-timeline">
        @foreach($deliveries as $dr)
            @php
                $isConfirmed = $dr->customer_confirmed ?? false;
                $isOut = !$isConfirmed && $dr->out_for_delivery_at;
                $badgeClass = $isConfirmed ? 'paid' : ($isOut ? 'partial' : $dr->status);
                $badgeText = $isConfirmed ? 'Confirmed' : ($isOut ? 'Out for Delivery' : ucfirst($dr->status));
                $itemCount = $dr->items->count();
            @endphp
            <a href="{{ route('portal.deliveries.show', $dr->id) }}" class="dv-tl-row" data-status="{{ $badgeClass }}">
                {{-- TIMELINE DOT + LINE --}}
                <div class="dv-tl-axis">
                    <div class="dv-tl-dot" data-status="{{ $badgeClass }}">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            @if($isConfirmed || $badgeClass === 'paid')
                                <path d="M5 12l5 5L20 7"/>
                            @elseif($badgeClass === 'pending')
                                <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                            @elseif($isOut)
                                <rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/>
                            @else
                                <circle cx="12" cy="12" r="10"/>
                            @endif
                        </svg>
                    </div>
                    <div class="dv-tl-line"></div>
                </div>

                {{-- CONTENT CARD --}}
                <div class="dv-tl-card">
                    <div class="dv-tl-head">
                        <div class="dv-tl-dr">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1.5"/></svg>
                            {{ $dr->dr_number }}
                        </div>
                        <span class="dv-tl-badge dv-tl-badge-{{ $badgeClass }}">{{ $badgeText }}</span>
                    </div>

                    @if($isOut && !$isConfirmed)
                        <div class="dv-tl-alert">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                            <span>Nasa daan na — Tap para i-confirm</span>
                        </div>
                    @endif

                    <div class="dv-tl-meta">
                        <div class="dv-tl-meta-item">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            {{ \Carbon\Carbon::parse($dr->delivery_date)->diffForHumans() }}
                        </div>
                        <div class="dv-tl-meta-item">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            {{ $itemCount }} item{{ $itemCount != 1 ? 's' : '' }}
                        </div>
                    </div>

                    <div class="dv-tl-foot">
                        <div class="dv-tl-amt">₱{{ number_format($dr->total_amount, 2) }}</div>
                        <div class="dv-tl-arrow">
                            View
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    @if(method_exists($deliveries, 'links') && $deliveries->hasPages())
        <div style="margin-top: 20px;">{{ $deliveries->withQueryString()->links() }}</div>
    @endif
@endif

@endsection

@push('styles')
<style>
/* ═══ DELIVERIES — VISUAL REDESIGN ═══ */

/* HERO CARD */
.dv-hero{position:relative;display:flex;align-items:center;justify-content:space-between;gap:14px;padding:20px;background:linear-gradient(135deg,#1A1A1A 0%,#2D2D2D 55%,#3D3D3D 100%);border-radius:18px;margin-bottom:14px;overflow:hidden;animation:fadeIn .35s ease both}
.dv-hero-glow{position:absolute;top:-80px;right:-80px;width:240px;height:240px;background:radial-gradient(circle,rgba(232,163,61,.4),transparent 70%);pointer-events:none;animation:heroGlow 5s ease-in-out infinite}
.dv-hero-pattern{position:absolute;inset:0;background-image:radial-gradient(circle at 15% 85%, rgba(255,255,255,.04) 1px, transparent 1px);background-size:18px 18px;pointer-events:none;opacity:.7}
@keyframes heroGlow{0%,100%{opacity:.7;transform:scale(1);}50%{opacity:1;transform:scale(1.15);}}
.dv-hero-content{position:relative;z-index:1;flex:1;min-width:0}
.dv-hero-label{display:inline-flex;align-items:center;gap:6px;font-size:10.5px;font-weight:800;color:rgba(255,255,255,.65);text-transform:uppercase;letter-spacing:.12em;margin-bottom:8px}
.dv-hero-label svg{color:#E8A33D}
.dv-hero-value{font-size:44px;font-weight:800;color:#FFFFFF;letter-spacing:-.045em;line-height:1;margin-bottom:12px;font-variant-numeric:tabular-nums}
.dv-hero-meta{display:flex;align-items:center;gap:6px;font-size:11.5px;color:rgba(255,255,255,.7);font-weight:600}
.dv-hero-dot{display:inline-block;width:6px;height:6px;border-radius:50%;box-shadow:0 0 6px currentColor}

.dv-hero-ring{position:relative;width:108px;height:108px;flex-shrink:0;z-index:1}
.dv-hero-ring svg{width:100%;height:100%;display:block}
.dv-hero-ring-text{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center}
.dv-hero-ring-num{font-size:22px;font-weight:800;color:#FFFFFF;letter-spacing:-.02em;line-height:1;font-variant-numeric:tabular-nums}
.dv-hero-ring-lbl{font-size:9px;font-weight:700;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:.1em;margin-top:4px}

/* QUICK STATS — horizontal scroll row */
.dv-quick{display:flex;gap:8px;margin-bottom:16px;overflow-x:auto;padding:2px 0 6px;scrollbar-width:none;-ms-overflow-style:none}
.dv-quick::-webkit-scrollbar{display:none}
.dv-quick-chip{display:flex;align-items:center;gap:10px;padding:10px 14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;flex-shrink:0;transition:all .15s}
.dv-quick-chip:hover{border-color:#D0D0D0;transform:translateY(-2px);box-shadow:0 6px 14px -6px rgba(0,0,0,.08)}
.dv-quick-icon{width:32px;height:32px;border-radius:9px;background:var(--bg,#F5F5F5);color:var(--c,#757575);display:grid;place-items:center;flex-shrink:0}
.dv-quick-value{font-size:15px;font-weight:800;color:#1A1A1A;letter-spacing:-.02em;line-height:1.1;font-variant-numeric:tabular-nums}
.dv-quick-label{font-size:9.5px;font-weight:700;color:#757575;text-transform:uppercase;letter-spacing:.08em;margin-top:1px}

/* SECTION LABEL */
.dv-section-label{display:flex;justify-content:space-between;align-items:center;margin:0 4px 12px}
.dv-section-label span:first-child{font-size:13px;font-weight:800;color:#1A1A1A;text-transform:uppercase;letter-spacing:.06em}
.dv-section-count{font-size:11px;font-weight:700;color:#757575;background:#F5F5F5;padding:3px 10px;border-radius:100px}

/* TIMELINE */
.dv-timeline{display:flex;flex-direction:column;animation:fadeIn .4s ease both}
.dv-tl-row{display:flex;gap:12px;text-decoration:none;color:inherit;padding-bottom:10px;position:relative}
.dv-tl-row:last-child .dv-tl-line{display:none}

/* AXIS (dot + line) */
.dv-tl-axis{position:relative;flex-shrink:0;width:32px;display:flex;flex-direction:column;align-items:center}
.dv-tl-dot{width:32px;height:32px;border-radius:50%;display:grid;place-items:center;flex-shrink:0;background:#FFFFFF;border:2px solid;position:relative;z-index:2;transition:transform .2s}
.dv-tl-dot[data-status="pending"]  { border-color:#F59E0B; color:#B45309; background:#FFF8E1; }
.dv-tl-dot[data-status="partial"]  { border-color:#2563EB; color:#1D4ED8; background:#EFF6FF; }
.dv-tl-dot[data-status="paid"],
.dv-tl-dot[data-status="confirmed"] { border-color:#16A34A; color:#15803D; background:#E8F5E9; }
.dv-tl-dot[data-status="cancelled"] { border-color:#DC2626; color:#B91C1C; background:#FFEBEE; }
.dv-tl-row:hover .dv-tl-dot{transform:scale(1.1)}

.dv-tl-line{flex:1;width:2px;background:linear-gradient(180deg, #E0E0E0, #F5F5F5);margin-top:4px;border-radius:2px;min-height:20px}

/* CARD */
.dv-tl-card{flex:1;min-width:0;padding:12px 14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:14px;transition:all .2s cubic-bezier(.4,0,.2,1)}
.dv-tl-row:hover .dv-tl-card{border-color:#D0D0D0;box-shadow:0 10px 20px -10px rgba(0,0,0,.1);transform:translateX(2px)}
.dv-tl-row:active .dv-tl-card{transform:scale(.995)}

.dv-tl-head{display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:8px}
.dv-tl-dr{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:800;color:#1A1A1A;font-family:ui-monospace,monospace;letter-spacing:-.01em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;min-width:0}
.dv-tl-dr svg{color:#9E9E9E;flex-shrink:0}
.dv-tl-badge{display:inline-flex;padding:3px 8px;border-radius:100px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;flex-shrink:0}
.dv-tl-badge-pending  { background:#FFF8E1; color:#B45309; }
.dv-tl-badge-partial  { background:#EFF6FF; color:#1D4ED8; }
.dv-tl-badge-paid,
.dv-tl-badge-confirmed { background:#E8F5E9; color:#15803D; }
.dv-tl-badge-cancelled { background:#FFEBEE; color:#B91C1C; }

.dv-tl-alert{display:flex;align-items:center;gap:8px;padding:8px 10px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:9px;font-size:10.5px;font-weight:700;color:#1D4ED8;margin-bottom:8px}
.dv-tl-alert svg{color:#2563EB;flex-shrink:0;animation:truckMove 1.5s ease-in-out infinite}
@keyframes truckMove{0%,100%{transform:translateX(0);}50%{transform:translateX(3px);}}

.dv-tl-meta{display:flex;gap:12px;margin-bottom:10px}
.dv-tl-meta-item{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;color:#757575;font-weight:600}
.dv-tl-meta-item svg{color:#9E9E9E;flex-shrink:0}

.dv-tl-foot{display:flex;justify-content:space-between;align-items:center;padding-top:10px;border-top:1px dashed #F0F0F0}
.dv-tl-amt{font-size:15px;font-weight:800;color:#D4901F;font-variant-numeric:tabular-nums;letter-spacing:-.02em}
.dv-tl-arrow{display:inline-flex;align-items:center;gap:3px;font-size:11px;font-weight:800;color:#D4901F;transition:transform .15s}
.dv-tl-row:hover .dv-tl-arrow{transform:translateX(3px)}

/* EMPTY */
.dv-empty{text-align:center;padding:56px 20px;background:#FFFFFF;border:1px dashed #E0E0E0;border-radius:16px}
.dv-empty-icon{width:72px;height:72px;margin:0 auto 16px;border-radius:18px;background:#FFF8EC;border:1px solid #F0E0C0;color:#D4901F;display:grid;place-items:center}
.dv-empty-title{font-size:15px;font-weight:800;color:#1A1A1A;margin-bottom:5px}
.dv-empty-text{font-size:12.5px;color:#757575;font-weight:500}

@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

@media (max-width: 480px) {
    .dv-hero{padding:18px}
    .dv-hero-value{font-size:38px}
    .dv-hero-ring{width:92px;height:92px}
    .dv-hero-ring-num{font-size:19px}
    .dv-tl-card{padding:11px 12px}
    .dv-tl-dr{font-size:11.5px}
    .dv-tl-amt{font-size:14px}
}
</style>
@endpush
