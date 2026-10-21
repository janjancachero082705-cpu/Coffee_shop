@extends('portal.layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $store = Auth::guard('store')->user();
    $totalOrders     = \App\Models\ReorderRequest::where('store_id', $store->id)->count();
    $pendingOrders   = \App\Models\ReorderRequest::where('store_id', $store->id)->where('status', 'pending')->count();
    $totalDelivered  = (float) \App\Models\DeliveryReceipt::where('store_id', $store->id)->sum('total_amount');
    $totalPaid       = (float) \App\Models\ConsignmentPayment::verified()->where('store_id', $store->id)->sum('amount');
    $balance         = max(0, $totalDelivered - $totalPaid);
    $deliveriesCount = \App\Models\DeliveryReceipt::where('store_id', $store->id)->count();
    $recentOrders    = \App\Models\ReorderRequest::where('store_id', $store->id)->latest()->take(3)->get();
    $paidPercent     = $totalDelivered > 0 ? min(100, round(($totalPaid / $totalDelivered) * 100)) : 100;
    $initials        = strtoupper(substr($store->store_name ?? 'ST', 0, 2));
    $firstName       = explode(' ', trim($store->owner_name ?? $store->store_name))[0];
@endphp

{{-- ═══ WELCOME HERO ═══ --}}
<div class="dash-hero">
    <div class="dash-hero-glow"></div>
    <div class="dash-hero-pattern"></div>

    <div class="dash-hero-content">
        <div class="dash-hero-greet">
            {{ now()->hour < 12 ? 'Good Morning' : (now()->hour < 18 ? 'Good Afternoon' : 'Good Evening') }},
        </div>
        <div class="dash-hero-name">{{ $firstName }} 👋</div>
        <div class="dash-hero-date">{{ now()->format('l, F j, Y') }}</div>
    </div>

    <div class="dash-hero-avatar">
        @if($store->logo_url)
            <img src="{{ $store->avatar_url }}" alt="{{ $store->store_name }}">
        @else
            {{ $initials }}
        @endif
    </div>
</div>

{{-- ═══ BALANCE CARD ═══ --}}
<div class="dash-balance">
    <div class="dash-balance-head">
        
<div class="dash-balance-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            Outstanding Balance
        </div>
        <span class="dash-balance-status {{ $balance > 0 ? 'pending' : 'paid' }}">
            {{ $balance > 0 ? 'Pending' : 'All Paid' }}
        </span>
    </div>

    <div class="dash-balance-value">
        <span class="dash-balance-cur">₱</span>
        <span class="dash-balance-num">{{ number_format($balance, 2) }}</span>
    </div>

    {{-- Progress Bar --}}
    <div class="dash-progress-wrap">
        <div class="dash-progress-info">
            <span>Payment Progress</span>
            <strong>{{ $paidPercent }}%</strong>
        </div>
        <div class="dash-progress-bar">
            <div class="dash-progress-fill" style="width: {{ $paidPercent }}%;"></div>
        </div>
    </div>

    {{-- Meta Grid --}}
    <div class="dash-balance-meta">
        <div class="dash-balance-meta-item">
            <div class="dash-meta-icon green">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            </div>
            <div class="dash-meta-content">
                <div class="dash-meta-lbl">Total Paid</div>
                <strong>₱{{ number_format($totalPaid, 0) }}</strong>
            </div>
        </div>
        <div class="dash-balance-meta-item">
            <div class="dash-meta-icon amber">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/></svg>
            </div>
            <div class="dash-meta-content">
                <div class="dash-meta-lbl">Delivered</div>
                <strong>₱{{ number_format($totalDelivered, 0) }}</strong>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="dash-balance-actions">
        <a href="{{ route('portal.orders.create') }}" class="dash-btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            New Order
        </a>
        <a href="{{ route('portal.payments.index') }}" class="dash-btn-ghost">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
            Pay Now
        </a>
    </div>
</div>

{{-- ═══ QUICK STATS ═══ --}}






<script>
    function markNotifAsRead(id, redirectUrl) {
        // I-send ang AJAX request para markahan as read
        fetch('/portal/notifications/' + id + '/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => {
            // Kapag success, redirect sa payment details
            window.location.href = redirectUrl;
        }).catch(() => {
            // Fallback: Redirect pa rin kahit mag-error ang AJAX
            window.location.href = redirectUrl;
        });
    }
</script>


<script>
    function markNotifAsRead(id, redirectUrl) {
        fetch('/portal/notifications/' + id + '/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => {
            window.location.href = redirectUrl;
        }).catch(() => {
            window.location.href = redirectUrl;
        });
    }
</script>
<!-- DASHBOARD NOTIFICATIONS -->
@php
    // Kukunin lang natin ang PINAKABAGO at UNREAD na payment alert
    $latestAlert = isset($storeNotifications) ? $storeNotifications->filter(function($notif) {
        $isPayment = str_contains($notif->title ?? '', 'Approved') || str_contains($notif->title ?? '', 'Rejected');
        return $isPayment && is_null($notif->read_at);
    })->first() : null;
@endphp

@if($latestAlert)
@php
    $isApprove = str_contains($latestAlert->title ?? '', 'Approved');
    $link = $latestAlert->link ?? route('portal.payments.index');
    
    // Kunin ang reason mula sa message (e.g., "Your payment of ₱3.00 was rejected. Reason: scam")
    $rejectReason = '';
    if (!$isApprove && isset($latestAlert->message)) {
        $parts = explode('Reason: ', $latestAlert->message);
        $rejectReason = count($parts) > 1 ? trim($parts[1]) : trim($latestAlert->message);
    }
@endphp

<style>
    @keyframes slideInDown {
        0% { opacity: 0; transform: translateY(-15px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulseRedAlert {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.2); }
        50% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
    }
    @keyframes pulseGreenAlert {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.2); }
        50% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
    }
    
    .premium-alert {
        display: flex; align-items: flex-start; gap: 14px; padding: 16px;
        border-radius: 12px; text-decoration: none; margin-bottom: 12px;
        animation: slideInDown 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        transition: transform 0.2s;
    }
    .premium-alert:active { transform: scale(0.98); }
    
    .premium-alert.rejected {
        background: #FEF2F2;
        border: 1.5px solid #FECACA;
        border-left: 4px solid #EF4444;
        animation: slideInDown 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards, pulseRedAlert 2s infinite 0.5s;
    }
    
    .premium-alert.approved {
        background: #F0FDF4;
        border: 1.5px solid #BBF7D0;
        border-left: 4px solid #10B981;
        animation: slideInDown 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards, pulseGreenAlert 2s infinite 0.5s;
    }
</style>

<div style="margin: 20px 0; padding: 0 4px;">
    <a href="{{ $link }}" class="premium-alert {{ $isApprove ? 'approved' : 'rejected' }}" 
       onclick="event.preventDefault(); markNotifAsRead({{ $latestAlert->id }}, '{{ $link }}');">
        
        <!-- Icon Container -->
        <div style="flex-shrink: 0; margin-top: 2px;">
            @if($isApprove)
                <svg width="24" height="24" fill="none" stroke="#10B981" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @else
                <svg width="24" height="24" fill="none" stroke="#EF4444" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            @endif
        </div>
        
        <!-- Content -->
        <div style="flex: 1;">
            <div style="font-size: 15px; font-weight: 800; color: {{ $isApprove ? '#065F46' : '#991B1B' }}; margin-bottom: 4px;">
                {{ $isApprove ? 'Payment Approved' : 'Payment Rejected' }}
            </div>
            <div style="font-size: 13px; color: {{ $isApprove ? '#047857' : '#7F1D1D' }}; font-weight: 500; line-height: 1.4;">
                @if($isApprove)
                    Your payment has been verified. Tap to view details.
                @else
                    Your previous payment was rejected.<br>
                    <span style="display:inline-block; margin-top:6px; padding: 6px 10px; background: #FEE2E2; border-radius: 6px; font-weight: 700; color: #991B1B;">
                        Reason: {{ $rejectReason ?: 'No reason provided' }}
                    </span>
                @endif
            </div>
        </div>
        
        <!-- Arrow Icon -->
        <div style="flex-shrink: 0; margin-left: auto; display: flex; align-items: center; color: {{ $isApprove ? '#10B981' : '#EF4444' }};">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
        </div>
    </a>
</div>

<script>
    function markNotifAsRead(id, redirectUrl) {
        // Palitan ang button para maging "loading" habang nag-aantay
        const alertEl = event.currentTarget;
        alertEl.style.opacity = '0.5';

        fetch('/portal/notifications/' + id + '/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => {
            window.location.href = redirectUrl;
        }).catch(() => {
            window.location.href = redirectUrl;
        });
    }
</script>
@endif
<!-- END DASHBOARD NOTIFICATIONS -->
<div class="dash-section-label">
    <span>Overview</span>
</div>
<div class="dash-stats">
    <a href="{{ route('portal.orders.index') }}" class="dash-stat">
        <div class="dash-stat-icon" style="--c:#E8A33D;--bg:#FFF8EC;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1.5"/></svg>
        </div>
        <div class="dash-stat-info">
            <div class="dash-stat-num">{{ $totalOrders }}</div>
            <div class="dash-stat-lbl">Orders</div>
        </div>
    </a>

    <a href="{{ route('portal.orders.index', ['status' => 'pending']) }}" class="dash-stat">
        <div class="dash-stat-icon" style="--c:#F59E0B;--bg:#FFF8E1;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="dash-stat-info">
            <div class="dash-stat-num">{{ $pendingOrders }}</div>
            <div class="dash-stat-lbl">Pending</div>
        </div>
    </a>

    <a href="{{ route('portal.deliveries.index') }}" class="dash-stat">
        <div class="dash-stat-icon" style="--c:#2563EB;--bg:#EFF6FF;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="dash-stat-info">
            <div class="dash-stat-num">{{ $deliveriesCount }}</div>
            <div class="dash-stat-lbl">Delivered</div>
        </div>
    </a>

    <a href="{{ route('portal.profile') }}" class="dash-stat">
        <div class="dash-stat-icon" style="--c:#10B981;--bg:#E8F5E9;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div class="dash-stat-info">
            <div class="dash-stat-num" style="font-size:13px;">Profile</div>
            <div class="dash-stat-lbl">Account</div>
        </div>
    </a>
</div>

{{-- ═══ PERKS / TIPS BANNER ═══ --}}
@if($pendingOrders > 0)
<div class="dash-alert">
    <div class="dash-alert-icon">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
    </div>
    <div class="dash-alert-content">
        <div class="dash-alert-title">{{ $pendingOrders }} order{{ $pendingOrders > 1 ? 's' : '' }} pending</div>
        <div class="dash-alert-text">Waiting for approval from supplier</div>
    </div>
    <a href="{{ route('portal.orders.index', ['status' => 'pending']) }}" class="dash-alert-btn">
        View
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
    </a>
</div>
@endif

{{-- ═══ RECENT ORDERS ═══ --}}
<div class="dash-section-label">
    <span>Recent Orders</span>
    <a href="{{ route('portal.orders.index') }}" class="dash-section-more">
        View All
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
    </a>
</div>

@if($recentOrders->isEmpty())
    <div class="dash-empty">
        <div class="dash-empty-icon">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1.5"/></svg>
        </div>
        <div class="dash-empty-title">No orders yet</div>
        <div class="dash-empty-text">Start by creating your first stock request</div>
        <a href="{{ route('portal.orders.create') }}" class="dash-btn-primary" style="display:inline-flex;width:auto;padding:0 20px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Create Order
        </a>
    </div>
@else
    <div class="dash-list">
        @foreach($recentOrders as $order)
            <a href="{{ route('portal.orders.show', $order->id) }}" class="dash-row">
                <div class="dash-row-icon" style="--c:#2563EB;--bg:#EFF6FF;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1.5"/></svg>
                </div>
                <div class="dash-row-content">
                    <div class="dash-row-title">{{ $order->request_number }}</div>
                    <div class="dash-row-sub">{{ $order->items->count() }} item{{ $order->items->count() != 1 ? 's' : '' }} · {{ $order->created_at->diffForHumans() }}</div>
                </div>
                <div class="dash-row-right">
                    <div class="dash-row-amt">₱{{ number_format($order->total_amount, 0) }}</div>
                    <span class="dash-row-badge {{ $order->status }}">{{ $order->status }}</span>
                </div>
            </a>
        @endforeach
    </div>
@endif

{{-- ═══ QUICK ACTIONS ═══ --}}
<div class="dash-section-label">
    <span>Quick Actions</span>
</div>
<div class="dash-quick-grid">
    <a href="{{ route('portal.orders.create') }}" class="dash-quick">
        <div class="dash-quick-icon" style="--c:#E8A33D;--bg:#FFF8EC;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        </div>
        <div class="dash-quick-label">New Order</div>
    </a>
    <a href="{{ route('portal.deliveries.index') }}" class="dash-quick">
        <div class="dash-quick-icon" style="--c:#2563EB;--bg:#EFF6FF;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/></svg>
        </div>
        <div class="dash-quick-label">Deliveries</div>
    </a>
    <a href="{{ route('portal.reports.index') }}" class="dash-quick">
        <div class="dash-quick-icon" style="--c:#10B981;--bg:#E8F5E9;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17V7M4 20h16M9 7a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7z"/></svg>
        </div>
        <div class="dash-quick-label">Reports</div>
    </a>
    <a href="{{ route('portal.payments.index') }}" class="dash-quick">
        <div class="dash-quick-icon" style="--c:#8B5CF6;--bg:#F3E8FF;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
        </div>
        <div class="dash-quick-label">Payments</div>
    </a>
</div>

@endsection

@push('styles')
<style>
/* ═══ DASHBOARD — ENHANCED FOODPANDA-STYLE ═══ */

/* WELCOME HERO */
.dash-hero{position:relative;display:flex;align-items:center;gap:14px;padding:20px;background:linear-gradient(135deg,#1A1A1A 0%,#2D2D2D 50%,#3D3D3D 100%);border-radius:16px;margin-bottom:20px;overflow:hidden;animation:fadeIn .3s ease both}
.dash-hero-glow{position:absolute;top:-80px;right:-80px;width:220px;height:220px;background:radial-gradient(circle,rgba(232,163,61,.35),transparent 70%);pointer-events:none;animation:heroGlow 5s ease-in-out infinite}
.dash-hero-pattern{position:absolute;inset:0;background-image:radial-gradient(circle at 20% 80%,rgba(255,255,255,.03) 1px,transparent 1px);background-size:20px 20px;pointer-events:none}
@keyframes heroGlow{0%,100%{opacity:.7;transform:scale(1);}50%{opacity:1;transform:scale(1.15);}}
.dash-hero-content{flex:1;position:relative;z-index:1;min-width:0}
.dash-hero-greet{font-size:12px;color:rgba(255,255,255,.7);font-weight:600;letter-spacing:.02em;margin-bottom:2px}
.dash-hero-name{font-size:22px;color:#FFFFFF;font-weight:800;letter-spacing:-.03em;line-height:1.15;margin-bottom:4px}
.dash-hero-date{font-size:11px;color:rgba(255,255,255,.6);font-weight:500}
.dash-hero-avatar{width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#E8A33D,#D4901F);color:#FFFFFF;display:grid;place-items:center;font-size:18px;font-weight:800;letter-spacing:-.02em;flex-shrink:0;overflow:hidden;border:2px solid rgba(255,255,255,.15);position:relative;z-index:1;box-shadow:0 8px 20px -6px rgba(232,163,61,.5)}
.dash-hero-avatar img{width:100%;height:100%;object-fit:cover}

/* BALANCE CARD */
.dash-balance{position:relative;padding:20px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:16px;margin-bottom:20px;animation:fadeIn .35s ease both;animation-delay:.05s}
.dash-balance-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.dash-balance-label{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:800;color:#757575;text-transform:uppercase;letter-spacing:.1em}
.dash-balance-label svg{color:#E8A33D}
.dash-balance-status{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:100px;font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.06em}
.dash-balance-status.pending{background:#FFF8E1;color:#B45309}
.dash-balance-status.paid{background:#E8F5E9;color:#15803D}
.dash-balance-status::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor}

.dash-balance-value{display:flex;align-items:baseline;gap:3px;margin-bottom:16px}
.dash-balance-cur{font-size:20px;font-weight:800;color:#9E9E9E}
.dash-balance-num{font-size:36px;font-weight:800;color:#1A1A1A;letter-spacing:-.04em;line-height:1;font-variant-numeric:tabular-nums}

/* PROGRESS BAR */
.dash-progress-wrap{margin-bottom:16px}
.dash-progress-info{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px}
.dash-progress-info span{font-size:11px;color:#757575;font-weight:600}
.dash-progress-info strong{font-size:12px;color:#1A1A1A;font-weight:800}
.dash-progress-bar{width:100%;height:6px;background:#F0F0F0;border-radius:100px;overflow:hidden}
.dash-progress-fill{height:100%;background:linear-gradient(90deg,#E8A33D,#D4901F);border-radius:100px;transition:width .6s cubic-bezier(.4,0,.2,1);box-shadow:0 0 12px rgba(232,163,61,.5)}

/* META GRID */
.dash-balance-meta{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px}
.dash-balance-meta-item{display:flex;align-items:center;gap:10px;padding:10px 12px;background:#FAFAFA;border:1px solid #F0F0F0;border-radius:10px}
.dash-meta-icon{width:28px;height:28px;border-radius:8px;display:grid;place-items:center;flex-shrink:0}
.dash-meta-icon.green{background:#E8F5E9;color:#15803D}
.dash-meta-icon.amber{background:#FFF8E1;color:#B45309}
.dash-meta-content{flex:1;min-width:0}
.dash-meta-lbl{font-size:9.5px;color:#757575;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:2px}
.dash-balance-meta-item strong{font-size:13px;font-weight:800;color:#1A1A1A;font-variant-numeric:tabular-nums;letter-spacing:-.02em}

/* BUTTONS */
.dash-balance-actions{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.dash-btn-primary,.dash-btn-ghost{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:44px;padding:0 16px;border-radius:100px;font-size:13px;font-weight:800;text-decoration:none;transition:all .15s;font-family:inherit;letter-spacing:-.01em}
.dash-btn-primary{background:#E8A33D;color:#FFFFFF;box-shadow:0 4px 14px rgba(232,163,61,.35)}
.dash-btn-primary:hover{background:#D4901F;box-shadow:0 6px 20px rgba(232,163,61,.45);transform:translateY(-1px)}
.dash-btn-primary:active{transform:scale(.97)}
.dash-btn-ghost{background:#FFFFFF;color:#1A1A1A;border:1.5px solid #1A1A1A}
.dash-btn-ghost:hover{background:#1A1A1A;color:#FFFFFF}
.dash-btn-ghost:active{transform:scale(.97)}

/* SECTION LABEL */
.dash-section-label{display:flex;justify-content:space-between;align-items:center;margin:0 4px 10px}
.dash-section-label span{font-size:13px;font-weight:800;color:#1A1A1A;letter-spacing:-.01em;text-transform:uppercase}
.dash-section-more{display:inline-flex;align-items:center;gap:2px;font-size:11.5px;font-weight:700;color:#1A1A1A;text-decoration:none;transition:color .15s}
.dash-section-more:hover{color:#D84315}

/* QUICK STATS */
.dash-stats{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:20px;animation:fadeIn .4s ease both;animation-delay:.1s}
.dash-stat{display:flex;align-items:center;gap:12px;padding:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;text-decoration:none;transition:all .15s;position:relative;overflow:hidden}
.dash-stat::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--c,#E8A33D);transform:scaleY(0);transition:transform .25s}
.dash-stat:hover{border-color:#D0D0D0;transform:translateY(-2px);box-shadow:0 6px 16px -6px rgba(0,0,0,.08)}
.dash-stat:hover::before{transform:scaleY(1)}
.dash-stat:active{transform:scale(.98)}
.dash-stat-icon{width:40px;height:40px;border-radius:10px;background:var(--bg,#FFF8EC);color:var(--c,#D4901F);display:grid;place-items:center;flex-shrink:0}
.dash-stat-info{flex:1;min-width:0}
.dash-stat-num{font-size:18px;font-weight:800;color:#1A1A1A;line-height:1;letter-spacing:-.02em;font-variant-numeric:tabular-nums;margin-bottom:3px}
.dash-stat-lbl{font-size:10.5px;color:#757575;text-transform:uppercase;letter-spacing:.06em;font-weight:700}

/* ALERT BANNER */
.dash-alert{display:flex;align-items:center;gap:12px;padding:14px;background:#FFF8E1;border:1px solid #FDE68A;border-radius:12px;margin-bottom:20px;animation:fadeIn .35s ease both}
.dash-alert-icon{width:36px;height:36px;border-radius:10px;background:#FFE082;color:#B45309;display:grid;place-items:center;flex-shrink:0}
.dash-alert-content{flex:1;min-width:0}
.dash-alert-title{font-size:13px;font-weight:800;color:#78350F;margin-bottom:2px}
.dash-alert-text{font-size:11.5px;color:#92400E;font-weight:500}
.dash-alert-btn{display:inline-flex;align-items:center;gap:3px;padding:8px 12px;background:#FFFFFF;color:#B45309;border-radius:100px;font-size:11.5px;font-weight:800;text-decoration:none;flex-shrink:0;transition:all .15s;border:1px solid #FDE68A}
.dash-alert-btn:hover{background:#B45309;color:#FFFFFF;border-color:#B45309}

/* LIST */
.dash-list{background:#FFF;border:1px solid #EEE;border-radius:12px;overflow:hidden;margin-bottom:20px;animation:fadeIn .4s ease both;animation-delay:.15s}
.dash-row{display:flex;align-items:center;gap:12px;padding:14px;text-decoration:none;color:inherit;background:#FFF;border-bottom:1px solid #F5F5F5;transition:background .15s;position:relative}
.dash-row:last-child{border-bottom:none}
.dash-row:hover{background:#FAFAFA}
.dash-row:hover .dash-row-right{transform:translateX(2px)}
.dash-row-icon{width:36px;height:36px;border-radius:10px;background:var(--bg,#EFF6FF);color:var(--c,#2563EB);display:grid;place-items:center;flex-shrink:0}
.dash-row-content{flex:1;min-width:0}
.dash-row-title{font-size:13px;font-weight:800;color:#1A1A1A;font-family:ui-monospace,monospace;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.dash-row-sub{font-size:11px;color:#757575;font-weight:500}
.dash-row-right{display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;transition:transform .15s}
.dash-row-amt{font-size:13px;font-weight:800;color:#1A1A1A;font-variant-numeric:tabular-nums;letter-spacing:-.02em}
.dash-row-badge{display:inline-flex;align-items:center;padding:3px 8px;border-radius:100px;font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.04em}
.dash-row-badge.pending{background:#FFF8E1;color:#B45309}
.dash-row-badge.approved{background:#E8F5E9;color:#15803D}
.dash-row-badge.rejected{background:#FFEBEE;color:#B91C1C}
.dash-row-badge.completed{background:#E8F5E9;color:#15803D}

/* EMPTY STATE */
.dash-empty{text-align:center;padding:32px 16px;background:#FFF;border:1px solid #EEE;border-radius:12px;margin-bottom:20px}
.dash-empty-icon{width:56px;height:56px;margin:0 auto 12px;border-radius:14px;background:#FFF8EC;border:1px solid #F0E0C0;display:grid;place-items:center;color:#D4901F}
.dash-empty-title{font-size:14px;font-weight:800;color:#1A1A1A;margin-bottom:4px}
.dash-empty-text{font-size:12px;color:#757575;margin-bottom:16px;font-weight:500}

/* QUICK ACTIONS */
.dash-quick-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;animation:fadeIn .4s ease both;animation-delay:.2s}
.dash-quick{display:flex;flex-direction:column;align-items:center;gap:8px;padding:14px 4px;background:#FFF;border:1px solid #EEE;border-radius:12px;text-decoration:none;transition:all .15s;position:relative;overflow:hidden}
.dash-quick::before{content:'';position:absolute;top:0;left:50%;width:0;height:3px;background:var(--c,#E8A33D);transition:all .25s;transform:translateX(-50%)}
.dash-quick:hover{border-color:#D0D0D0;transform:translateY(-2px);box-shadow:0 6px 16px -6px rgba(0,0,0,.08)}
.dash-quick:hover::before{width:100%}
.dash-quick:active{transform:scale(.97)}
.dash-quick-icon{width:38px;height:38px;border-radius:10px;background:var(--bg,#FFF8EC);color:var(--c,#D4901F);display:grid;place-items:center;transition:transform .2s}
.dash-quick:hover .dash-quick-icon{transform:scale(1.1)}
.dash-quick-label{font-size:11px;font-weight:700;color:#1A1A1A;text-align:center;line-height:1.2}

@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

@media(max-width:480px){
.dash-hero{padding:16px}
.dash-hero-name{font-size:19px}
.dash-hero-avatar{width:48px;height:48px;font-size:16px}
.dash-balance-num{font-size:30px}
.dash-balance-cur{font-size:17px}
.dash-stat{padding:12px}
.dash-stat-num{font-size:16px}
.dash-quick-grid{grid-template-columns:repeat(4,1fr)}
.dash-quick{padding:12px 2px}
.dash-quick-label{font-size:10px}
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var topbarInner = document.querySelector('.p-topbar-inner');
    if (topbarInner) {
        topbarInner.innerHTML = '<div class="p-topbar-store-text"><div class="p-topbar-store-name" style="font-size:16px;font-weight:800;">Dashboard</div></div><a href="{{ route('portal.profile') }}" class="dash-topbar-btn" aria-label="Profile"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></a>';
    }
});
</script>
<style>
.dash-topbar-btn{display:grid;place-items:center;width:36px;height:36px;color:#1A1A1A;text-decoration:none;border-radius:50%;transition:background .15s;flex-shrink:0}
.dash-topbar-btn:hover{background:#F5F5F5}
.dash-topbar-btn svg{width:20px;height:20px}
</style>
@endpush
