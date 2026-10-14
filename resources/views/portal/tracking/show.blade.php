@extends('portal.layouts.app')

@section('title', 'Track ' . $order->request_number)

@section('content')

@php
    $dr = $order->deliveryReceipt;
    $isApproved = !empty($order->approved_at);
    $hasDelivery = $dr ? true : false;
    $isOut = $hasDelivery && !empty($dr->out_for_delivery_at);
    $isReceived = $hasDelivery && !empty($dr->customer_confirmed);
    $isPaid = $hasDelivery && ($dr->amount_paid >= $dr->total_amount);
    $isRejected = strtolower($order->status) === 'rejected';

    $currentStatus = $isRejected ? 'rejected' : ($isPaid ? 'paid' : ($isReceived ? 'received' : ($isOut ? 'transit' : ($isApproved ? 'approved' : 'pending'))));

    $steps = [
        ['label' => 'Order Placed',      'desc' => 'Nagsugod na ang imong order request',                                  'time' => $order->created_at,                            'done' => true,        'active' => false,          'icon' => 'placed'],
        ['label' => 'Approved by Admin', 'desc' => $isApproved ? 'Gi-approve na sa admin ang imong request' : ($isRejected ? 'Gi-reject ang order' : 'Gahulat pa sa approval sa admin'), 'time' => $isApproved ? $order->approved_at : null, 'done' => $isApproved, 'active' => !$isApproved && !$isRejected, 'icon' => $isRejected ? 'rejected' : 'approved'],
        ['label' => 'Out for Delivery',  'desc' => $isOut ? 'Nasa daan na ang imong order padulong sa imong store' : 'Gi-prepare pa para i-deliver', 'time' => $isOut ? $dr->out_for_delivery_at : null, 'done' => $isOut, 'active' => $isApproved && !$isOut && !$isRejected, 'icon' => 'truck'],
        ['label' => 'Received',          'desc' => $isReceived ? 'Na-confirm na nadawat nimo ang mga items' : 'Hulat sa confirmation nga nadawat na', 'time' => $isReceived ? $dr->confirmed_at : null, 'done' => $isReceived, 'active' => $isOut && !$isReceived, 'icon' => 'received'],
        ['label' => 'Paid',              'desc' => $isPaid ? 'Fully paid na ang order' : 'Balance: ₱' . number_format($hasDelivery ? max(0, $dr->total_amount - $dr->amount_paid) : $order->total_amount, 2), 'time' => null, 'done' => $isPaid, 'active' => $isReceived && !$isPaid, 'icon' => 'paid'],
    ];
@endphp

<a href="{{ route('portal.tracking.index') }}" class="td-back">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Back to Tracking
</a>

<div class="td-header" data-status="{{ $currentStatus }}">
    <div class="td-header-top">
        <div class="td-header-num">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1.5"/></svg>
            {{ $order->request_number }}
        </div>
        <div class="td-header-amt">₱{{ number_format($order->total_amount, 2) }}</div>
    </div>
    <div class="td-header-meta">
        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        {{ $order->created_at->format('M d, Y · g:i A') }}
        <span style="opacity:.4;">·</span>
        {{ $order->items->count() }} item{{ $order->items->count() != 1 ? 's' : '' }}
    </div>
</div>

@if($isRejected)
    <div class="td-rejected">
        <div class="td-rejected-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </div>
        <div>
            <div class="td-rejected-title">Order Rejected</div>
            <div class="td-rejected-desc">{{ $order->rejection_reason ?? 'Gi-reject ang imong order sa admin' }}</div>
        </div>
    </div>
@endif

<div class="td-section-label">Delivery Progress</div>
<div class="td-timeline">
    @foreach($steps as $i => $step)
        @php $stepClass = $step['done'] ? 'done' : ($step['active'] ? 'active' : 'pending'); @endphp
        <div class="td-step td-step-{{ $stepClass }}">
            <div class="td-step-axis">
                <div class="td-step-dot {{ $stepClass }}">
                    @if($step['done'])
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" stroke-linecap="round"><path d="M5 12l5 5L20 7"/></svg>
                    @elseif($step['active'])
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            @if($step['icon'] === 'approved')
                                <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                            @elseif($step['icon'] === 'truck')
                                <rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                            @elseif($step['icon'] === 'received')
                                <path d="M20 6L9 17l-5-5"/>
                            @elseif($step['icon'] === 'paid')
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                            @endif
                        </svg>
                    @else
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            @if($step['icon'] === 'approved' || $step['icon'] === 'rejected')
                                <circle cx="12" cy="12" r="10"/>
                            @elseif($step['icon'] === 'truck')
                                <rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/>
                            @elseif($step['icon'] === 'received')
                                <path d="M20 6L9 17l-5-5"/>
                            @elseif($step['icon'] === 'paid')
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                            @endif
                        </svg>
                    @endif
                </div>
                @if($i < count($steps) - 1)
                    <div class="td-step-line {{ $step['done'] ? 'done' : '' }}"></div>
                @endif
            </div>
            <div class="td-step-content">
                <div class="td-step-title">{{ $step['label'] }}</div>
                <div class="td-step-desc">{{ $step['desc'] }}</div>
                @if($step['time'])
                    <div class="td-step-time">
                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        {{ \Carbon\Carbon::parse($step['time'])->format('M d, Y · g:i A') }}
                    </div>
                @endif
                @if($step['active'])
                    <div class="td-step-active-badge">
                        <span class="td-step-pulse"></span>
                        In Progress
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="td-section-label">Order Summary</div>
<div class="td-list">
    @foreach($order->items as $item)
        <div class="td-item">
            <div class="td-item-thumb">
                @if($item->product && $item->product->image_url)
                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                @else
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                @endif
            </div>
            <div class="td-item-info">
                <div class="td-item-name">{{ $item->product->name ?? '-' }}</div>
                <div class="td-item-meta">{{ $item->quantity_requested }} × ₱{{ number_format($item->unit_price, 2) }}</div>
            </div>
            <div class="td-item-total">₱{{ number_format($item->subtotal, 2) }}</div>
        </div>
    @endforeach
</div>

<div class="td-total">
    <span class="td-total-lbl">Total</span>
    <span class="td-total-val">₱{{ number_format($order->total_amount, 2) }}</span>
</div>

@endsection

@push('styles')
<style>
.td-back{display:inline-flex;align-items:center;gap:5px;padding:8px 14px;margin-bottom:14px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:100px;color:#757575;font-size:12px;font-weight:700;text-decoration:none;transition:all .15s}
.td-back:hover{border-color:#D0D0D0;color:#1A1A1A;transform:translateX(-2px)}

.td-header{position:relative;padding:18px;margin-bottom:16px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:16px;overflow:hidden;animation:fadeIn .3s ease both}
.td-header::before{content:'';position:absolute;top:0;left:0;right:0;height:4px}
.td-header[data-status="pending"]::before{background:linear-gradient(90deg,#F59E0B,#FBBF24)}
.td-header[data-status="approved"]::before{background:linear-gradient(90deg,#15803D,#4ADE80)}
.td-header[data-status="transit"]::before{background:linear-gradient(90deg,#2563EB,#60A5FA)}
.td-header[data-status="received"]::before{background:linear-gradient(90deg,#7C3AED,#A78BFA)}
.td-header[data-status="paid"]::before{background:linear-gradient(90deg,#15803D,#4ADE80)}
.td-header[data-status="rejected"]::before{background:linear-gradient(90deg,#DC2626,#EF4444)}
.td-header-top{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:10px}
.td-header-num{display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:800;color:#1A1A1A;font-family:ui-monospace,monospace;letter-spacing:-.01em;flex:1;min-width:0;word-break:break-all}
.td-header-num svg{color:#9E9E9E;flex-shrink:0}
.td-header-amt{font-size:20px;font-weight:800;color:#D4901F;font-variant-numeric:tabular-nums;letter-spacing:-.02em;flex-shrink:0}
.td-header-meta{display:flex;align-items:center;gap:5px;font-size:11px;color:#757575;font-weight:600;flex-wrap:wrap}
.td-header-meta svg{color:#9E9E9E;flex-shrink:0}

.td-rejected{display:flex;align-items:flex-start;gap:12px;padding:14px;margin-bottom:16px;background:#FFEBEE;border:1px solid #FECACA;border-left:3px solid #DC2626;border-radius:14px}
.td-rejected-icon{width:36px;height:36px;border-radius:10px;background:#FEE2E2;color:#B91C1C;display:grid;place-items:center;flex-shrink:0;border:1px solid #FECACA}
.td-rejected-title{font-size:13.5px;font-weight:800;color:#B91C1C;margin-bottom:3px}
.td-rejected-desc{font-size:11.5px;color:#991B1B;line-height:1.5;font-weight:500}

.td-section-label{font-size:11px;font-weight:800;color:#757575;text-transform:uppercase;letter-spacing:.1em;margin:0 4px 12px}

.td-timeline{background:#FFFFFF;border:1px solid #EEEEEE;border-radius:16px;padding:22px 18px;margin-bottom:18px;animation:fadeIn .35s ease both;animation-delay:.05s}
.td-step{display:flex;gap:14px;position:relative;padding-bottom:20px}
.td-step:last-child{padding-bottom:0}
.td-step-axis{display:flex;flex-direction:column;align-items:center;flex-shrink:0;width:44px}
.td-step-dot{width:44px;height:44px;border-radius:50%;display:grid;place-items:center;background:#F5F5F5;border:2px solid #E0E0E0;color:#B8B8B8;flex-shrink:0;position:relative;z-index:2;transition:all .3s cubic-bezier(.34,1.4,.64,1)}
.td-step-dot.done{background:#E8A33D;border-color:#E8A33D;color:#FFFFFF;box-shadow:0 0 0 5px rgba(232,163,61,.15),0 4px 12px -2px rgba(232,163,61,.4)}
.td-step-dot.active{background:#FFFFFF;border-color:#E8A33D;color:#E8A33D;box-shadow:0 0 0 5px rgba(232,163,61,.2);animation:tdPulse 2s ease-in-out infinite}
@keyframes tdPulse{0%,100%{box-shadow:0 0 0 5px rgba(232,163,61,.2);}50%{box-shadow:0 0 0 10px rgba(232,163,61,.08);}}

.td-step-line{flex:1;width:2px;background:#F0F0F0;margin:4px 0;border-radius:2px;min-height:30px;transition:background .3s}
.td-step-line.done{background:linear-gradient(180deg,#E8A33D,#F0B85A);box-shadow:0 0 6px rgba(232,163,61,.3)}

.td-step-content{flex:1;min-width:0;padding-top:6px}
.td-step-title{font-size:14px;font-weight:800;color:#B8B8B8;letter-spacing:-.01em;margin-bottom:4px;transition:color .3s;line-height:1.3}
.td-step.done .td-step-title{color:#1A1A1A}
.td-step.active .td-step-title{color:#D4901F}
.td-step-desc{font-size:12px;color:#B8B8B8;font-weight:500;line-height:1.5;transition:color .3s}
.td-step.done .td-step-desc,
.td-step.active .td-step-desc{color:#757575}
.td-step-time{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;color:#9E9E9E;font-weight:600;margin-top:8px;font-family:ui-monospace,monospace;padding:4px 8px;background:#FAFAFA;border-radius:6px;transition:all .3s}
.td-step.done .td-step-time{background:#FFF8EC;color:#B45309}
.td-step-active-badge{display:inline-flex;align-items:center;gap:6px;margin-top:8px;padding:4px 10px;background:#FFF8EC;border:1px solid #FDE68A;border-radius:100px;font-size:10px;font-weight:800;color:#B45309;text-transform:uppercase;letter-spacing:.06em}
.td-step-pulse{width:6px;height:6px;border-radius:50%;background:#E8A33D;animation:tdDotPulse 1.5s ease-in-out infinite}
@keyframes tdDotPulse{0%,100%{transform:scale(1);opacity:1;}50%{transform:scale(1.5);opacity:.5;}}

.td-list{background:#FFFFFF;border:1px solid #EEEEEE;border-radius:12px;overflow:hidden;margin-bottom:14px;animation:fadeIn .35s ease both;animation-delay:.1s}
.td-item{display:flex;align-items:center;gap:12px;padding:12px 14px;border-bottom:1px solid #F5F5F5}
.td-item:last-child{border-bottom:none}
.td-item-thumb{width:42px;height:42px;border-radius:10px;background:#F5F5F5;border:1px solid #EEEEEE;display:grid;place-items:center;color:#B8A29A;overflow:hidden;flex-shrink:0}
.td-item-thumb img{width:100%;height:100%;object-fit:cover}
.td-item-info{flex:1;min-width:0}
.td-item-name{font-size:12.5px;font-weight:700;color:#1A1A1A;margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.td-item-meta{font-size:11px;color:#757575;font-weight:500}
.td-item-total{font-size:13.5px;font-weight:800;color:#D4901F;font-variant-numeric:tabular-nums;flex-shrink:0;letter-spacing:-.02em}

.td-total{display:flex;justify-content:space-between;align-items:center;padding:16px;background:linear-gradient(135deg,#FFF8EC,#FFFCF5);border:1px solid #F0E0C0;border-radius:12px;margin-bottom:14px;position:relative;overflow:hidden;animation:fadeIn .35s ease both;animation-delay:.15s}
.td-total::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#E8A33D,#F0B85A)}
.td-total-lbl{font-size:11px;font-weight:800;color:#92400E;text-transform:uppercase;letter-spacing:.08em}
.td-total-val{font-size:22px;font-weight:800;color:#D4901F;font-variant-numeric:tabular-nums;letter-spacing:-.03em}

@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

@media(max-width:480px){
.td-header{padding:16px}
.td-header-amt{font-size:18px}
.td-timeline{padding:18px 14px}
.td-step-dot{width:38px;height:38px}
.td-step-dot svg{width:18px;height:18px}
.td-step-axis{width:38px}
.td-step-title{font-size:13px}
.td-step-desc{font-size:11.5px}
.td-total-val{font-size:20px}
}
</style>
@endpush