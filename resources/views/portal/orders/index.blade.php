@extends('portal.layouts.app')

@section('title', 'My Orders')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $total    = \App\Models\ReorderRequest::where('store_id', $store->id)->count();
    $pending  = \App\Models\ReorderRequest::where('store_id', $store->id)->where('status', 'pending')->count();
    $approved = \App\Models\ReorderRequest::where('store_id', $store->id)->where('status', 'approved')->count();
    $rejected = \App\Models\ReorderRequest::where('store_id', $store->id)->where('status', 'rejected')->count();
    $status   = request('status', 'all');
    $totalAmount = \App\Models\ReorderRequest::where('store_id', $store->id)->sum('total_amount');
    $approvalRate = $total > 0 ? round(($approved / $total) * 100) : 0;
@endphp

{{-- PAGE HEADER --}}
<div class="ord-head">
    <div class="ord-head-text">
        <div class="ord-title">My Orders</div>
        <div class="ord-sub">Track your stock requests</div>
    </div>
    <a href="{{ route('portal.orders.create') }}" class="ord-cta">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
        New
    </a>
</div>

{{-- HERO STATS CARD --}}
<div class="ord-hero">
    <div class="ord-hero-glow"></div>
    <div class="ord-hero-content">
        <div class="ord-hero-label">Total Orders</div>
        <div class="ord-hero-value">{{ $total }}</div>
        <div class="ord-hero-meta">
            <span class="ord-hero-meta-item">
                <span class="ord-hero-meta-dot green"></span>
                {{ $approved }} approved
            </span>
            @if($pending > 0)
            <span class="ord-hero-meta-item">
                <span class="ord-hero-meta-dot amber"></span>
                {{ $pending }} pending
            </span>
            @endif
        </div>
    </div>
    <div class="ord-hero-ring">
        <svg viewBox="0 0 100 100" class="ord-hero-ring-svg">
            <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(255,255,255,.15)" stroke-width="8"/>
            <circle cx="50" cy="50" r="42" fill="none" stroke="#E8A33D" stroke-width="8" stroke-linecap="round"
                    stroke-dasharray="264" stroke-dashoffset="{{ 264 - (264 * $approvalRate / 100) }}"
                    transform="rotate(-90 50 50)"/>
        </svg>
        <div class="ord-hero-ring-text">
            <div class="ord-hero-ring-num">{{ $approvalRate }}%</div>
            <div class="ord-hero-ring-lbl">Approved</div>
        </div>
    </div>
</div>

{{-- MINI STATS --}}
<div class="ord-mini-grid">
    <div class="ord-mini">
        <div class="ord-mini-icon" style="--c:#F59E0B;--bg:#FFF8E1;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="ord-mini-info">
            <div class="ord-mini-num">{{ $pending }}</div>
            <div class="ord-mini-lbl">Pending</div>
        </div>
    </div>
    <div class="ord-mini">
        <div class="ord-mini-icon" style="--c:#15803D;--bg:#E8F5E9;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <div class="ord-mini-info">
            <div class="ord-mini-num">{{ $approved }}</div>
            <div class="ord-mini-lbl">Approved</div>
        </div>
    </div>
    <div class="ord-mini">
        <div class="ord-mini-icon" style="--c:#B91C1C;--bg:#FFEBEE;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
        </div>
        <div class="ord-mini-info">
            <div class="ord-mini-num">{{ $rejected }}</div>
            <div class="ord-mini-lbl">Rejected</div>
        </div>
    </div>
</div>

{{-- FILTER TABS --}}
<div class="ord-tabs">
    <a href="{{ route('portal.orders.index') }}" class="ord-tab {{ $status === 'all' ? 'active' : '' }}">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>
        All
    </a>
    <a href="{{ route('portal.orders.index', ['status' => 'pending']) }}" class="ord-tab {{ $status === 'pending' ? 'active' : '' }}">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        Pending
    </a>
    <a href="{{ route('portal.orders.index', ['status' => 'approved']) }}" class="ord-tab {{ $status === 'approved' ? 'active' : '' }}">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        Approved
    </a>
    <a href="{{ route('portal.orders.index', ['status' => 'rejected']) }}" class="ord-tab {{ $status === 'rejected' ? 'active' : '' }}">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
        Rejected
    </a>
</div>

{{-- ORDERS GRID --}}
@if($orders->isEmpty())
    <div class="ord-empty">
        <div class="ord-empty-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1.5"/></svg>
        </div>
        <div class="ord-empty-title">
            @if($status !== 'all') Walang {{ $status }} orders @else No orders yet @endif
        </div>
        <div class="ord-empty-text">
            @if($status !== 'all') Try checking other status filters @else Start by creating your first stock request @endif
        </div>
        <a href="{{ route('portal.orders.create') }}" class="ord-empty-btn">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Create Order
        </a>
    </div>
@else
    <div class="ord-grid">
        @foreach($orders as $order)
            @php
                $st = strtolower($order->status);
                $badgeMap = [
                    'pending' => ['label' => 'Pending', 'class' => 'pending', 'color' => '#F59E0B'],
                    'approved' => ['label' => 'Approved', 'class' => 'approved', 'color' => '#15803D'],
                    'rejected' => ['label' => 'Rejected', 'class' => 'rejected', 'color' => '#B91C1C'],
                ];
                $badge = $badgeMap[$st] ?? ['label' => ucfirst($st), 'class' => 'default', 'color' => '#757575'];
                $itemCount = $order->items->count();
            @endphp
            <a href="{{ route('portal.orders.show', $order->id) }}" class="ord-card" style="--sc:{{ $badge['color'] }};">
                {{-- Status stripe sa taas --}}
                <div class="ord-card-stripe" data-status="{{ $badge['class'] }}"></div>

                {{-- Top section --}}
                <div class="ord-card-top">
                    <div class="ord-card-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1.5"/></svg>
                    </div>
                    <span class="ord-card-badge ord-card-badge-{{ $badge['class'] }}">
                        <span class="ord-card-badge-dot"></span>
                        {{ $badge['label'] }}
                    </span>
                </div>

                {{-- Order number --}}
                <div class="ord-card-num">{{ $order->request_number }}</div>

                {{-- Meta --}}
                <div class="ord-card-meta">
                    <span class="ord-card-meta-item">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ $order->created_at->format('M d') }}
                    </span>
                    <span class="ord-card-meta-item">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        {{ $order->created_at->diffForHumans() }}
                    </span>
                </div>

                {{-- Bottom: amount + items --}}
                <div class="ord-card-bottom">
                    <div class="ord-card-amount">
                        <span class="ord-card-amount-cur">₱</span>
                        <span class="ord-card-amount-num">{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="ord-card-items">
                        {{ $itemCount }} item{{ $itemCount != 1 ? 's' : '' }}
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    @if(method_exists($orders, 'links') && $orders->hasPages())
        <div style="margin-top: 20px;">{{ $orders->withQueryString()->links() }}</div>
    @endif
@endif

@endsection

@push('styles')
<style>
/* ═══ ORDERS — VISUAL CARD GRID ═══ */

/* HEADER */
.ord-head{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding:4px 4px 18px}
.ord-head-text{flex:1;min-width:0}
.ord-title{font-size:26px;font-weight:800;color:#1A1A1A;letter-spacing:-.035em;line-height:1.15;margin-bottom:3px}
.ord-sub{font-size:13px;color:#757575;font-weight:500}
.ord-cta{display:inline-flex;align-items:center;gap:6px;padding:10px 16px;background:#E8A33D;border-radius:100px;color:#FFFFFF;font-size:13px;font-weight:800;text-decoration:none;box-shadow:0 4px 14px rgba(232,163,61,.35);white-space:nowrap;transition:all .15s;flex-shrink:0}
.ord-cta:hover{background:#D4901F;transform:translateY(-1px);box-shadow:0 6px 20px rgba(232,163,61,.45)}

/* HERO STATS CARD */
.ord-hero{position:relative;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:20px;background:linear-gradient(135deg,#1A1A1A 0%,#2D2D2D 50%,#3D3D3D 100%);border-radius:16px;margin-bottom:14px;overflow:hidden;animation:fadeIn .3s ease both}
.ord-hero-glow{position:absolute;top:-80px;right:-80px;width:220px;height:220px;background:radial-gradient(circle,rgba(232,163,61,.35),transparent 70%);pointer-events:none;animation:heroGlow 5s ease-in-out infinite}
@keyframes heroGlow{0%,100%{opacity:.7;transform:scale(1);}50%{opacity:1;transform:scale(1.15);}}
.ord-hero-content{position:relative;z-index:1;flex:1;min-width:0}
.ord-hero-label{font-size:11px;font-weight:800;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px}
.ord-hero-value{font-size:42px;font-weight:800;color:#FFFFFF;letter-spacing:-.04em;line-height:1;margin-bottom:10px;font-variant-numeric:tabular-nums}
.ord-hero-meta{display:flex;flex-direction:column;gap:4px}
.ord-hero-meta-item{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:rgba(255,255,255,.75)}
.ord-hero-meta-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0}
.ord-hero-meta-dot.green{background:#4ADE80;box-shadow:0 0 8px rgba(74,222,128,.8)}
.ord-hero-meta-dot.amber{background:#FBBF24;box-shadow:0 0 8px rgba(251,191,36,.8)}
.ord-hero-ring{position:relative;width:100px;height:100px;flex-shrink:0;z-index:1}
.ord-hero-ring-svg{width:100%;height:100%;display:block}
.ord-hero-ring-text{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center}
.ord-hero-ring-num{font-size:20px;font-weight:800;color:#FFFFFF;letter-spacing:-.02em;line-height:1;font-variant-numeric:tabular-nums}
.ord-hero-ring-lbl{font-size:8.5px;font-weight:700;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.08em;margin-top:3px}

/* MINI STATS */
.ord-mini-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:16px;animation:fadeIn .3s ease both;animation-delay:.05s}
.ord-mini{display:flex;align-items:center;gap:10px;padding:12px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;transition:all .15s}
.ord-mini:hover{border-color:#D0D0D0;transform:translateY(-2px);box-shadow:0 6px 16px -6px rgba(0,0,0,.08)}
.ord-mini-icon{width:32px;height:32px;border-radius:9px;background:var(--bg,#F5F5F5);color:var(--c,#757575);display:grid;place-items:center;flex-shrink:0}
.ord-mini-info{flex:1;min-width:0}
.ord-mini-num{font-size:17px;font-weight:800;color:#1A1A1A;line-height:1;letter-spacing:-.02em;font-variant-numeric:tabular-nums;margin-bottom:2px}
.ord-mini-lbl{font-size:10px;color:#757575;font-weight:700;text-transform:uppercase;letter-spacing:.06em}

/* FILTER TABS */
.ord-tabs{display:flex;gap:6px;margin-bottom:16px;overflow-x:auto;padding:2px 0 6px;scrollbar-width:none;-ms-overflow-style:none}
.ord-tabs::-webkit-scrollbar{display:none}
.ord-tab{display:inline-flex;align-items:center;gap:5px;padding:8px 14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:100px;font-size:12.5px;font-weight:700;color:#757575;text-decoration:none;white-space:nowrap;transition:all .15s;flex-shrink:0}
.ord-tab:hover{border-color:#D0D0D0;color:#1A1A1A}
.ord-tab.active{background:#1A1A1A;color:#FFFFFF;border-color:#1A1A1A}
.ord-tab svg{opacity:.7}
.ord-tab.active svg{opacity:1}

/* ORDERS GRID */
.ord-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;animation:fadeIn .35s ease both;animation-delay:.1s}

/* ORDER CARD */
.ord-card{position:relative;display:flex;flex-direction:column;padding:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:14px;text-decoration:none;color:inherit;transition:all .2s cubic-bezier(.4,0,.2,1);overflow:hidden}
.ord-card::before{content:'';position:absolute;inset:0;border-radius:14px;box-shadow:0 0 0 0 var(--sc,#E8A33D);transition:box-shadow .2s;pointer-events:none}
.ord-card:hover{transform:translateY(-3px);box-shadow:0 12px 24px -10px rgba(0,0,0,.12);border-color:transparent}
.ord-card:hover::before{box-shadow:0 0 0 1.5px var(--sc,#E8A33D)}
.ord-card:active{transform:scale(.98)}

.ord-card-stripe{position:absolute;top:0;left:0;right:0;height:3px;border-radius:14px 14px 0 0}
.ord-card-stripe[data-status="pending"]{background:linear-gradient(90deg,#F59E0B,#FBBF24)}
.ord-card-stripe[data-status="approved"]{background:linear-gradient(90deg,#15803D,#4ADE80)}
.ord-card-stripe[data-status="rejected"]{background:linear-gradient(90deg,#B91C1C,#EF4444)}
.ord-card-stripe[data-status="default"]{background:linear-gradient(90deg,#757575,#9E9E9E)}

.ord-card-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;margin-top:4px}
.ord-card-icon{width:36px;height:36px;border-radius:10px;background:#F5F5F5;color:#1A1A1A;display:grid;place-items:center;flex-shrink:0}
.ord-card-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:100px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.04em}
.ord-card-badge-dot{width:5px;height:5px;border-radius:50%;flex-shrink:0;background:currentColor}
.ord-card-badge-pending{background:#FFF8E1;color:#B45309}
.ord-card-badge-approved{background:#E8F5E9;color:#15803D}
.ord-card-badge-rejected{background:#FFEBEE;color:#B91C1C}
.ord-card-badge-default{background:#F5F5F5;color:#757575}

.ord-card-num{font-size:13px;font-weight:800;color:#1A1A1A;font-family:ui-monospace,monospace;letter-spacing:-.01em;margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ord-card-meta{display:flex;flex-direction:column;gap:3px;margin-bottom:12px}
.ord-card-meta-item{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;color:#757575;font-weight:600}
.ord-card-meta-item svg{color:#9E9E9E;flex-shrink:0}

.ord-card-bottom{display:flex;justify-content:space-between;align-items:flex-end;margin-top:auto;padding-top:12px;border-top:1px dashed #F0F0F0}
.ord-card-amount{display:flex;align-items:baseline;gap:1px}
.ord-card-amount-cur{font-size:12px;font-weight:800;color:#9E9E9E}
.ord-card-amount-num{font-size:16px;font-weight:800;color:#1A1A1A;letter-spacing:-.03em;font-variant-numeric:tabular-nums}
.ord-card-items{font-size:10.5px;color:#757575;font-weight:700;padding:3px 8px;background:#F5F5F5;border-radius:100px}

/* EMPTY STATE */
.ord-empty{text-align:center;padding:48px 20px;background:#FFFFFF;border:1px dashed #E0E0E0;border-radius:14px}
.ord-empty-icon{width:64px;height:64px;margin:0 auto 14px;border-radius:16px;background:#FFF8EC;border:1px solid #F0E0C0;color:#D4901F;display:grid;place-items:center}
.ord-empty-title{font-size:15px;font-weight:800;color:#1A1A1A;margin-bottom:5px;letter-spacing:-.01em}
.ord-empty-text{font-size:12.5px;color:#757575;margin-bottom:18px;font-weight:500}
.ord-empty-btn{display:inline-flex;align-items:center;gap:6px;padding:12px 20px;background:#E8A33D;border-radius:100px;color:#FFFFFF;font-size:13px;font-weight:800;text-decoration:none;box-shadow:0 4px 14px rgba(232,163,61,.35);transition:all .15s}
.ord-empty-btn:hover{background:#D4901F;transform:translateY(-1px);box-shadow:0 6px 20px rgba(232,163,61,.45)}
.ord-empty-btn:active{transform:scale(.97)}

@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

/* RESPONSIVE */
@media(max-width:520px){
.ord-grid{grid-template-columns:1fr}
.ord-hero-ring{width:88px;height:88px}
.ord-hero-ring-num{font-size:18px}
}
@media(max-width:480px){
.ord-title{font-size:22px}
.ord-hero{padding:16px}
.ord-hero-value{font-size:36px}
.ord-hero-ring{width:80px;height:80px}
.ord-hero-ring-num{font-size:16px}
.ord-hero-ring-lbl{font-size:8px}
.ord-mini{padding:10px;gap:8px}
.ord-mini-icon{width:28px;height:28px}
.ord-mini-num{font-size:15px}
.ord-mini-lbl{font-size:9px}
.ord-tab{padding:7px 12px;font-size:12px}
.ord-card{padding:12px}
.ord-card-icon{width:32px;height:32px}
.ord-card-num{font-size:12px}
.ord-card-amount-num{font-size:15px}
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var topbarInner = document.querySelector('.p-topbar-inner');
    if (topbarInner) {
        topbarInner.innerHTML = '<div class="p-topbar-store-text"><div class="p-topbar-store-name" style="font-size:16px;font-weight:800;">My Orders</div></div><a href="{{ route('portal.orders.create') }}" class="ord-topbar-btn" aria-label="New Order"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></a>';
    }
});
</script>
<style>
.ord-topbar-btn{display:grid;place-items:center;width:36px;height:36px;color:#D4901F;text-decoration:none;border-radius:50%;transition:background .15s;flex-shrink:0}
.ord-topbar-btn:hover{background:#FFF8EC}
.ord-topbar-btn svg{width:20px;height:20px}
</style>
@endpush
