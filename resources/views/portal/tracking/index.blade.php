@extends('portal.layouts.app')

@section('title', 'Order Tracking')

@section('content')

@php
    $store = Auth::guard('store')->user();

    $filter = request('filter', 'active');
    $query = \App\Models\ReorderRequest::where('store_id', $store->id)->with(['deliveryReceipt', 'items']);

    if ($filter === 'active') {
        $query->whereIn('status', ['pending', 'approved']);
    } elseif ($filter === 'rejected') {
        $query->where('status', 'rejected');
    } elseif ($filter === 'in_transit') {
        $query->whereHas('deliveryReceipt', function ($q) {
            $q->whereNotNull('out_for_delivery_at')->whereNull('customer_confirmed');
        });
    } elseif ($filter === 'awaiting_payment') {
        $query->whereHas('deliveryReceipt', function ($q) {
            $q->whereNotNull('customer_confirmed')->whereRaw('amount_paid < total_amount');
        });
    } elseif ($filter === 'completed') {
        $query->whereHas('deliveryReceipt', function ($q) {
            $q->whereNotNull('customer_confirmed')->whereRaw('amount_paid >= total_amount');
        });
    }

    $orders = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

    $allCount = \App\Models\ReorderRequest::where('store_id', $store->id)->count();
    $activeCount = \App\Models\ReorderRequest::where('store_id', $store->id)
        ->whereIn('status', ['pending', 'approved'])->count();
    $rejectedCount = \App\Models\ReorderRequest::where('store_id', $store->id)
        ->where('status', 'rejected')->count();
    $inTransitCount = \App\Models\ReorderRequest::where('store_id', $store->id)
        ->whereHas('deliveryReceipt', function ($q) {
            $q->whereNotNull('out_for_delivery_at')->whereNull('customer_confirmed');
        })->count();
    $awaitingPaymentCount = \App\Models\ReorderRequest::where('store_id', $store->id)
        ->whereHas('deliveryReceipt', function ($q) {
            $q->whereNotNull('customer_confirmed')->whereRaw('amount_paid < total_amount');
        })->count();
    $completedCount = \App\Models\ReorderRequest::where('store_id', $store->id)
        ->whereHas('deliveryReceipt', function ($q) {
            $q->whereNotNull('customer_confirmed')->whereRaw('amount_paid >= total_amount');
        })->count();
@endphp

{{-- HERO --}}
<div class="tk-hero">
    <div class="tk-hero-glow"></div>
    <div class="tk-hero-content">
        <div class="tk-hero-label">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            Live Tracking
        </div>
        <div class="tk-hero-title">Track Your Orders</div>
        <div class="tk-hero-sub">Real-time updates sa imong consignment</div>
        <div class="tk-hero-stats">
            <div class="tk-hero-stat">
                <div class="tk-hero-stat-val">{{ $inTransitCount }}</div>
                <div class="tk-hero-stat-lbl">In Transit</div>
            </div>
            <div class="tk-hero-stat-divider"></div>
            <div class="tk-hero-stat">
                <div class="tk-hero-stat-val">{{ $awaitingPaymentCount }}</div>
                <div class="tk-hero-stat-lbl">To Pay</div>
            </div>
            <div class="tk-hero-stat-divider"></div>
            <div class="tk-hero-stat">
                <div class="tk-hero-stat-val">{{ $completedCount }}</div>
                <div class="tk-hero-stat-lbl">Completed</div>
            </div>
        </div>
    </div>
    <div class="tk-hero-icon">
        <svg width="80" height="80" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" opacity=".3"/>
            <circle cx="12" cy="12" r="6" opacity=".5"/>
            <circle cx="12" cy="12" r="2"/>
            <path d="M12 2v4M12 18v4M2 12h4M18 12h4" opacity=".4"/>
        </svg>
    </div>
</div>

{{-- FILTER TABS --}}
<div class="tk-tabs">
    <a href="{{ route('portal.tracking.index', ['filter' => 'active']) }}" class="tk-tab {{ $filter === 'active' ? 'active' : '' }}">
        <span class="tk-tab-dot" style="background:#FBBF24;"></span>
        Active
        @if($activeCount > 0)<span class="tk-tab-count">{{ $activeCount }}</span>@endif
    </a>
    <a href="{{ route('portal.tracking.index', ['filter' => 'in_transit']) }}" class="tk-tab {{ $filter === 'in_transit' ? 'active' : '' }}">
        <span class="tk-tab-dot" style="background:#2563EB;"></span>
        In Transit
        @if($inTransitCount > 0)<span class="tk-tab-count">{{ $inTransitCount }}</span>@endif
    </a>
    <a href="{{ route('portal.tracking.index', ['filter' => 'awaiting_payment']) }}" class="tk-tab {{ $filter === 'awaiting_payment' ? 'active' : '' }}">
        <span class="tk-tab-dot" style="background:#D4901F;"></span>
        To Pay
        @if($awaitingPaymentCount > 0)<span class="tk-tab-count">{{ $awaitingPaymentCount }}</span>@endif
    </a>
    <a href="{{ route('portal.tracking.index', ['filter' => 'completed']) }}" class="tk-tab {{ $filter === 'completed' ? 'active' : '' }}">
        <span class="tk-tab-dot" style="background:#15803D;"></span>
        Completed
        @if($completedCount > 0)<span class="tk-tab-count">{{ $completedCount }}</span>@endif
    </a>
    <a href="{{ route('portal.tracking.index', ['filter' => 'rejected']) }}" class="tk-tab {{ $filter === 'rejected' ? 'active' : '' }}">
        <span class="tk-tab-dot" style="background:#DC2626;"></span>
        Rejected
        @if($rejectedCount > 0)<span class="tk-tab-count">{{ $rejectedCount }}</span>@endif
    </a>
</div>

{{-- LIST --}}
@if($orders->isEmpty())
    <div class="tk-empty">
        <div class="tk-empty-icon">
            <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="tk-empty-title">
            @if($filter === 'active') No active orders
            @elseif($filter === 'in_transit') Walang in transit
            @elseif($filter === 'awaiting_payment') Walang to pay
            @elseif($filter === 'completed') Walang completed
            @elseif($filter === 'rejected') No rejected orders
            @else No orders yet @endif
        </div>
        <div class="tk-empty-text">Wala kay transaction sa filter na ito.</div>
    </div>
@else
    <div class="tk-list">
        @foreach($orders as $order)
            @php
                $dr = $order->deliveryReceipt;
                $isApproved = !empty($order->approved_at);
                $hasDelivery = $dr ? true : false;
                $isOut = $hasDelivery && !empty($dr->out_for_delivery_at);
                $isReceived = $hasDelivery && !empty($dr->customer_confirmed);
                $isPaid = $hasDelivery && ($dr->amount_paid >= $dr->total_amount);
                $isRejected = strtolower($order->status) === 'rejected';

                if ($isRejected) {
                    $currentStep = 0;
                    $currentLabel = 'Rejected';
                    $currentColor = '#DC2626';
                    $currentBg = '#FFEBEE';
                } elseif ($isPaid) {
                    $currentStep = 5;
                    $currentLabel = 'Completed';
                    $currentColor = '#15803D';
                    $currentBg = '#E8F5E9';
                } elseif ($isReceived) {
                    $currentStep = 4;
                    $currentLabel = 'To Pay';
                    $currentColor = '#D4901F';
                    $currentBg = '#FFF8EC';
                } elseif ($isOut) {
                    $currentStep = 3;
                    $currentLabel = 'In Transit';
                    $currentColor = '#2563EB';
                    $currentBg = '#EFF6FF';
                } elseif ($isApproved) {
                    $currentStep = 2;
                    $currentLabel = 'Approved';
                    $currentColor = '#15803D';
                    $currentBg = '#E8F5E9';
                } else {
                    $currentStep = 1;
                    $currentLabel = 'Pending';
                    $currentColor = '#B45309';
                    $currentBg = '#FFF8E1';
                }

                $steps = [
                    ['label' => 'Placed', 'done' => true, 'time' => $order->created_at],
                    ['label' => 'Approved', 'done' => $isApproved, 'time' => $order->approved_at ?? null],
                    ['label' => 'Out', 'done' => $isOut, 'time' => $isOut ? $dr->out_for_delivery_at : null],
                    ['label' => 'Received', 'done' => $isReceived, 'time' => $isReceived ? $dr->confirmed_at : null],
                    ['label' => 'Paid', 'done' => $isPaid, 'time' => null],
                ];
            @endphp

            <a href="{{ route('portal.tracking.show', $order->id) }}" class="tk-card">
                <div class="tk-card-stripe" style="background: {{ $currentColor }};"></div>

                {{-- HEADER --}}
                <div class="tk-card-head">
                    <div class="tk-card-num">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1.5"/></svg>
                        {{ $order->request_number }}
                    </div>
                    <span class="tk-badge" style="background: {{ $currentBg }}; color: {{ $currentColor }};">
                        {{ $currentLabel }}
                    </span>
                </div>

                @if(!$isRejected)
                    {{-- VERTICAL CHECKLIST --}}
                    <div class="tk-checklist">
                        @foreach($steps as $i => $step)
                            <div class="tk-check-item {{ $step['done'] ? 'done' : '' }} {{ $i + 1 === $currentStep ? 'current' : '' }}">
                                <div class="tk-check-axis">
                                    <div class="tk-check-dot">
                                        @if($step['done'])
                                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24" stroke-linecap="round"><path d="M5 12l5 5L20 7"/></svg>
                                        @else
                                            <span class="tk-check-num">{{ $i + 1 }}</span>
                                        @endif
                                    </div>
                                    @if($i < count($steps) - 1)
                                        <div class="tk-check-line"></div>
                                    @endif
                                </div>
                                <div class="tk-check-info">
                                    <div class="tk-check-label">{{ $step['label'] }}</div>
                                    @if($step['time'])
                                        <div class="tk-check-time">{{ \Carbon\Carbon::parse($step['time'])->format('M d · g:i A') }}</div>
                                    @elseif($i + 1 === $currentStep)
                                        <div class="tk-check-time">In progress...</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="tk-card-rejected">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        <span>{{ $order->rejection_reason ?? 'Order was rejected' }}</span>
                    </div>
                @endif

                {{-- FOOTER --}}
                <div class="tk-card-foot">
                    <div class="tk-card-meta">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ $order->created_at->format('M d, Y') }}
                        <span style="opacity:.4;">·</span>
                        {{ $order->items->count() }} item{{ $order->items->count() != 1 ? 's' : '' }}
                    </div>
                    <div class="tk-card-amt">₱{{ number_format($order->total_amount, 0) }}</div>
                </div>

                <div class="tk-card-cta">
                    <span>View Details</span>
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                </div>
            </a>
        @endforeach
    </div>

    @if($orders->hasPages())
        <div style="margin-top: 20px;">{{ $orders->links() }}</div>
    @endif
@endif

@endsection

@push('styles')
<style>
/* ═══ TRACKING INDEX — CHECKLIST STYLE ═══ */

.tk-hero{position:relative;padding:22px 20px;background:linear-gradient(135deg,#1A1A1A 0%,#2D2D2D 55%,#3D3D3D 100%);border-radius:20px;margin-bottom:16px;overflow:hidden;animation:fadeIn .35s ease both}
.tk-hero-glow{position:absolute;top:-100px;right:-100px;width:280px;height:280px;background:radial-gradient(circle,rgba(232,163,61,.45),transparent 65%);pointer-events:none;animation:tkGlow 6s ease-in-out infinite}
@keyframes tkGlow{0%,100%{opacity:.7;transform:scale(1);}50%{opacity:1;transform:scale(1.2);}}
.tk-hero-content{position:relative;z-index:2}
.tk-hero-label{display:inline-flex;align-items:center;gap:6px;font-size:10.5px;font-weight:800;color:#E8A33D;text-transform:uppercase;letter-spacing:.12em;margin-bottom:10px;padding:5px 10px;background:rgba(232,163,61,.15);border-radius:100px;border:1px solid rgba(232,163,61,.3)}
.tk-hero-title{font-size:24px;font-weight:800;color:#FFFFFF;letter-spacing:-.03em;line-height:1.15;margin-bottom:6px}
.tk-hero-sub{font-size:12.5px;color:rgba(255,255,255,.6);font-weight:500;margin-bottom:20px}
.tk-hero-stats{display:flex;align-items:center;gap:12px;padding:14px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:14px;backdrop-filter:blur(8px);position:relative;z-index:2}
.tk-hero-stat{flex:1;text-align:center}
.tk-hero-stat-val{font-size:22px;font-weight:800;color:#FFFFFF;letter-spacing:-.02em;line-height:1;font-variant-numeric:tabular-nums}
.tk-hero-stat-lbl{font-size:9.5px;font-weight:700;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:.08em;margin-top:4px}
.tk-hero-stat-divider{width:1px;height:32px;background:rgba(255,255,255,.12)}
.tk-hero-icon{position:absolute;top:50%;right:-10px;transform:translateY(-50%);color:rgba(255,255,255,.08);z-index:1;pointer-events:none;animation:tkSpin 30s linear infinite}
@keyframes tkSpin{to{transform:translateY(-50%) rotate(360deg);}}

.tk-tabs{display:flex;gap:6px;margin-bottom:16px;overflow-x:auto;padding:2px 0 6px;scrollbar-width:none}
.tk-tabs::-webkit-scrollbar{display:none}
.tk-tab{display:inline-flex;align-items:center;gap:7px;padding:10px 14px;background:#FFFFFF;border:1.5px solid #EEEEEE;border-radius:100px;font-size:12px;font-weight:700;color:#757575;text-decoration:none;white-space:nowrap;transition:all .2s;flex-shrink:0}
.tk-tab:hover{border-color:#D0D0D0;color:#1A1A1A;transform:translateY(-1px)}
.tk-tab.active{background:#1A1A1A;color:#FFFFFF;border-color:#1A1A1A;box-shadow:0 6px 16px -6px rgba(0,0,0,.3)}
.tk-tab.active .tk-tab-count{background:rgba(255,255,255,.2);color:#FFFFFF}
.tk-tab-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}
.tk-tab-count{display:inline-flex;align-items:center;justify-content:center;padding:2px 7px;background:#F5F5F5;color:#757575;font-size:10.5px;font-weight:800;border-radius:100px;min-width:20px}

.tk-list{display:flex;flex-direction:column;gap:12px;animation:fadeIn .4s ease both}

/* CARD */
.tk-card{position:relative;display:block;padding:16px;background:#FFFFFF;border:1px solid #EEEEEE;border-radius:16px;text-decoration:none;color:inherit;transition:all .25s cubic-bezier(.4,0,.2,1);overflow:hidden;cursor:pointer}
.tk-card:hover{transform:translateY(-3px);box-shadow:0 16px 32px -12px rgba(0,0,0,.12);border-color:transparent}
.tk-card:active{transform:scale(.995)}
.tk-card-stripe{position:absolute;top:0;left:0;right:0;height:4px;border-radius:16px 16px 0 0}

.tk-card-head{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:14px;margin-top:2px}
.tk-card-num{display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:800;color:#1A1A1A;font-family:ui-monospace,monospace;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;min-width:0;letter-spacing:-.01em}
.tk-card-num svg{color:#9E9E9E;flex-shrink:0}
.tk-badge{display:inline-flex;padding:4px 10px;border-radius:100px;font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;flex-shrink:0}

/* CHECKLIST */
.tk-checklist{padding:4px 0 8px}
.tk-check-item{display:flex;gap:12px;align-items:flex-start;position:relative;padding-bottom:14px}
.tk-check-item:last-child{padding-bottom:0}
.tk-check-axis{display:flex;flex-direction:column;align-items:center;flex-shrink:0;width:26px;position:relative}
.tk-check-dot{width:26px;height:26px;border-radius:50%;display:grid;place-items:center;background:#F5F5F5;border:2px solid #E0E0E0;color:#B8B8B8;flex-shrink:0;transition:all .3s;position:relative;z-index:2}
.tk-check-item.done .tk-check-dot{background:#E8A33D;border-color:#E8A33D;color:#FFFFFF;box-shadow:0 0 0 3px rgba(232,163,61,.15)}
.tk-check-item.current .tk-check-dot{background:#FFFFFF;border-color:#E8A33D;color:#E8A33D;box-shadow:0 0 0 3px rgba(232,163,61,.25);animation:tkDotPulse 2s ease-in-out infinite}
@keyframes tkDotPulse{0%,100%{box-shadow:0 0 0 3px rgba(232,163,61,.25);}50%{box-shadow:0 0 0 6px rgba(232,163,61,.1);}}
.tk-check-num{font-size:10px;font-weight:800;color:inherit;line-height:1}
.tk-check-line{position:absolute;top:26px;left:50%;transform:translateX(-50%);width:2px;height:calc(100% - 22px);background:#F0F0F0;border-radius:2px;transition:background .3s}
.tk-check-item.done .tk-check-line{background:linear-gradient(180deg,#E8A33D,#F0B85A)}
.tk-check-item.current .tk-check-line{background:linear-gradient(180deg,#E8A33D,#F0F0F0)}
.tk-check-info{flex:1;min-width:0;padding-top:4px}
.tk-check-label{font-size:12px;font-weight:800;color:#B8B8B8;letter-spacing:-.01em;margin-bottom:2px;transition:color .3s;line-height:1.3}
.tk-check-item.done .tk-check-label{color:#1A1A1A}
.tk-check-item.current .tk-check-label{color:#D4901F}
.tk-check-time{font-size:10px;color:#9E9E9E;font-weight:600;font-family:ui-monospace,monospace;transition:color .3s}
.tk-check-item.done .tk-check-time{color:#B45309}
.tk-check-item.current .tk-check-time{color:#D4901F;font-weight:700}

.tk-card-rejected{display:flex;align-items:center;gap:8px;padding:10px 12px;background:#FFEBEE;border-radius:10px;font-size:11.5px;color:#B91C1C;font-weight:600;margin-bottom:14px;line-height:1.4}
.tk-card-rejected svg{flex-shrink:0}

.tk-card-foot{display:flex;justify-content:space-between;align-items:center;padding-top:12px;border-top:1px dashed #F0F0F0;gap:10px;margin-top:4px}
.tk-card-meta{display:flex;align-items:center;gap:5px;font-size:10.5px;color:#757575;font-weight:600;flex-wrap:wrap;flex:1;min-width:0}
.tk-card-meta svg{color:#9E9E9E;flex-shrink:0}
.tk-card-amt{font-size:16px;font-weight:800;color:#D4901F;font-variant-numeric:tabular-nums;letter-spacing:-.02em;flex-shrink:0}

.tk-card-cta{display:flex;align-items:center;justify-content:space-between;padding:10px 12px;margin-top:10px;background:linear-gradient(135deg,#FFF8EC,#FFFCF5);border:1px solid #F0E0C0;border-radius:10px;font-size:11.5px;font-weight:800;color:#D4901F;text-transform:uppercase;letter-spacing:.06em;transition:all .2s}
.tk-card:hover .tk-card-cta{background:linear-gradient(135deg,#E8A33D,#F0B85A);border-color:#E8A33D;color:#FFFFFF}
.tk-card-cta svg{transition:transform .2s}
.tk-card:hover .tk-card-cta svg{transform:translateX(3px)}

.tk-empty{text-align:center;padding:56px 20px;background:#FFFFFF;border:1px dashed #E0E0E0;border-radius:16px}
.tk-empty-icon{width:72px;height:72px;margin:0 auto 16px;border-radius:18px;background:#FFF8EC;border:1px solid #F0E0C0;color:#D4901F;display:grid;place-items:center}
.tk-empty-title{font-size:15px;font-weight:800;color:#1A1A1A;margin-bottom:5px}
.tk-empty-text{font-size:12.5px;color:#757575;font-weight:500}

@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

@media(max-width:480px){
.tk-hero{padding:18px}
.tk-hero-title{font-size:20px}
.tk-hero-stat-val{font-size:19px}
.tk-hero-icon{width:60px;height:60px}
.tk-card{padding:14px}
.tk-check-dot{width:22px;height:22px}
.tk-check-dot svg{width:10px;height:10px}
.tk-check-num{font-size:9px}
.tk-check-line{top:22px}
.tk-check-label{font-size:11.5px}
.tk-check-time{font-size:9.5px}
.tk-card-amt{font-size:15px}
.tk-tab{padding:9px 12px;font-size:11.5px}
}
</style>
@endpush