@extends('layouts.admin')

@section('title', $delivery->dr_number)
@section('subtitle', 'Delivery Receipt details')

@section('content')

@php
    $isConfirmed = $delivery->customer_confirmed ?? false;
    $isOut       = !$isConfirmed && $delivery->out_for_delivery_at;
    $isPending   = !$isConfirmed && !$isOut;

    if ($isConfirmed) {
        $statusConfig = [
            'label' => 'Delivered',
            'color' => '#22c55e',
            'icon'  => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>',
        ];
    } elseif ($isOut) {
        $statusConfig = [
            'label' => 'Out for Delivery',
            'color' => '#3b82f6',
            'icon'  => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
        ];
    } else {
        $statusConfig = [
            'label' => 'Pending',
            'color' => '#f59e0b',
            'icon'  => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
        ];
    }

    $canRecordPayment = ($delivery->balance > 0) && $isConfirmed;
@endphp

{{-- ═══════════════════════════════════════════════ --}}
{{-- BACK BUTTON                                    --}}
{{-- ═══════════════════════════════════════════════ --}}
<a href="{{ route('deliveries.index') }}" class="dv-back">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
    <span>Back to Deliveries</span>
</a>

{{-- ═══════════════════════════════════════════════ --}}
{{-- HERO                                            --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="dv-hero" style="--status-color: {{ $statusConfig['color'] }};">
    <div class="dv-hero-grid"></div>
    <div class="dv-hero-accent"></div>

    <div class="dv-hero-inner">
        <div class="dv-hero-left">
            <div class="dv-hero-icon" style="color:{{ $statusConfig['color'] }}; background:{{ $statusConfig['color'] }}1a; border-color:{{ $statusConfig['color'] }}40;">
                {!! $statusConfig['icon'] !!}
            </div>
            <div class="dv-hero-info">
                <div class="dv-hero-eyebrow" style="color:{{ $statusConfig['color'] }};">{{ $statusConfig['label'] }}</div>
                <h1 class="dv-hero-title">{{ $delivery->dr_number }}</h1>
                <div class="dv-hero-meta">
                    <a href="{{ route('stores.show', $delivery->store_id) }}" class="dv-hero-link">
                        {{ $delivery->store->store_name ?? '-' }}
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                    </a>
                    @if($delivery->store->code ?? null)
                        <span class="dv-hero-dot">·</span>
                        <span class="dv-hero-code">{{ $delivery->store->code }}</span>
                    @endif
                    <span class="dv-hero-dot">·</span>
                    <span class="dv-hero-date">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="dv-hero-right">
            <div class="dv-hero-stat">
                <div class="dv-hero-stat-label">Total</div>
                <div class="dv-hero-stat-value">&#8369;{{ number_format($delivery->total_amount, 2) }}</div>
            </div>
            <div class="dv-hero-stat-divider"></div>
            <div class="dv-hero-stat">
                <div class="dv-hero-stat-label">Paid</div>
                <div class="dv-hero-stat-value green">&#8369;{{ number_format($delivery->amount_paid, 2) }}</div>
            </div>
            <div class="dv-hero-stat-divider"></div>
            <div class="dv-hero-stat">
                <div class="dv-hero-stat-label">Balance</div>
                <div class="dv-hero-stat-value {{ $delivery->balance > 0 ? 'amber' : 'green' }}">&#8369;{{ number_format($delivery->balance, 2) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- GRID                                            --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="dv-grid">

    {{-- LEFT: Items + Notes --}}
    <div class="dv-col">

        <div class="dv-card">
            <div class="dv-card-head">
                <div class="dv-card-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="dv-card-header-text">
                    <div class="dv-card-title">Delivery Items</div>
                    <div class="dv-card-sub">{{ $delivery->items->count() }} product(s)</div>
                </div>
            </div>

            <div class="dv-items">
                @foreach($delivery->items as $item)
                    <div class="dv-item">
                        <div class="dv-item-thumb">
                            @if($item->product && $item->product->image_url)
                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                            @else
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            @endif
                        </div>
                        <div class="dv-item-info">
                            <div class="dv-item-name">{{ $item->product->name ?? '-' }}</div>
                            <div class="dv-item-meta">
                                @if($item->product->sku ?? null)
                                    <span class="dv-item-sku">{{ $item->product->sku }}</span>
                                @endif
                                <span class="dv-item-qty">{{ $item->quantity_delivered }} × &#8369;{{ number_format($item->unit_price, 2) }}</span>
                            </div>
                        </div>
                        <div class="dv-item-total">&#8369;{{ number_format($item->subtotal, 2) }}</div>
                    </div>
                @endforeach
            </div>

            <div class="dv-total-row">
                <span class="dv-total-label">Total</span>
                <span class="dv-total-value">&#8369;{{ number_format($delivery->total_amount, 2) }}</span>
            </div>
        </div>

        @if($delivery->notes)
            <div class="dv-card">
                <div class="dv-card-head">
                    <div class="dv-card-icon">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 7h16M4 12h16M4 17h10"/>
                        </svg>
                    </div>
                    <div class="dv-card-title">Notes</div>
                </div>
                <div class="dv-notes">{{ $delivery->notes }}</div>
            </div>
        @endif
    </div>

    {{-- RIGHT: Payment + Details + Actions --}}
    <div class="dv-col">

        {{-- PAYMENT STATUS --}}
        <div class="dv-card">
            <div class="dv-card-head">
                <div class="dv-card-icon green">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                    </svg>
                </div>
                <div class="dv-card-title">Payment Status</div>
            </div>

            <div class="dv-payment-rows">
                <div class="dv-payment-row">
                    <span class="dv-payment-label">Total Amount</span>
                    <span class="dv-payment-value">&#8369;{{ number_format($delivery->total_amount, 2) }}</span>
                </div>
                <div class="dv-payment-row">
                    <span class="dv-payment-label">Amount Paid</span>
                    <span class="dv-payment-value green">− &#8369;{{ number_format($delivery->amount_paid, 2) }}</span>
                </div>
            </div>

            <div class="dv-balance-total {{ $delivery->balance > 0 ? 'is-owed' : 'is-clear' }}">
                <div class="dv-balance-total-label">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        @if($delivery->balance > 0)
                            <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                        @else
                            <path d="M5 12l5 5L20 7"/>
                        @endif
                    </svg>
                    {{ $delivery->balance > 0 ? 'Unpaid Balance' : 'Fully Paid' }}
                </div>
                <div class="dv-balance-total-value">&#8369;{{ number_format($delivery->balance, 2) }}</div>
            </div>

            @if($delivery->balance > 0)
                @if($canRecordPayment)
                    <a href="{{ route('consignment.payments.create') }}?store_id={{ $delivery->store_id }}&delivery_receipt_id={{ $delivery->id }}"
                       class="dv-action-btn dv-action-primary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Record Payment
                    </a>
                @else
                    @if(!$delivery->customer_confirmed && !$delivery->out_for_delivery_at)
                        <button type="button" class="dv-action-btn dv-action-ship" onclick="openShipModal()">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <rect x="1" y="3" width="15" height="13" rx="1"/>
                                <path d="M16 8h4l3 3v5h-7V8z"/>
                                <circle cx="5.5" cy="18.5" r="2.5"/>
                                <circle cx="18.5" cy="18.5" r="2.5"/>
                            </svg>
                            Mark as Out for Delivery
                        </button>
                    @endif

                    @if(!$delivery->customer_confirmed && $delivery->out_for_delivery_at)
                        <button type="button" disabled class="dv-action-btn dv-action-disabled">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                            Waiting for Customer Confirmation
                        </button>
                    @endif

                    <div class="dv-action-hint">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <span>Dili pa ma-record ang payment. Kinahanglan mo-confirm una ang customer nga nadawat niya ang products.</span>
                    </div>
                @endif
            @endif
        </div>

        {{-- DETAILS --}}
        <div class="dv-card">
            <div class="dv-card-head">
                <div class="dv-card-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4M12 8h.01"/>
                    </svg>
                </div>
                <div class="dv-card-title">Details</div>
            </div>

            <div class="dv-detail-rows">
                <div class="dv-detail-row">
                    <span class="dv-detail-label">Created by</span>
                    <span class="dv-detail-value">{{ $delivery->user->name ?? '-' }}</span>
                </div>
                <div class="dv-detail-row">
                    <span class="dv-detail-label">Created at</span>
                    <span class="dv-detail-value">{{ \Carbon\Carbon::parse($delivery->created_at)->format('M d, Y · g:i A') }}</span>
                </div>
                <div class="dv-detail-row">
                    <span class="dv-detail-label">Last updated</span>
                    <span class="dv-detail-value">{{ $delivery->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="dv-card">
            <div class="dv-card-head">
                <div class="dv-card-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                    </svg>
                </div>
                <div class="dv-card-title">Actions</div>
            </div>

            <div class="dv-action-list">
                <a href="{{ route('deliveries.edit', $delivery) }}" class="dv-action-btn dv-action-ghost">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit Delivery
                </a>
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- SHIP MODAL                                     --}}
{{-- ═══════════════════════════════════════════════ --}}
<div id="shipModal" class="dv-mo-backdrop" onclick="if(event.target === this) closeShipModal()">
    <div class="dv-mo">
        <div class="dv-mo-header">
            <div class="dv-mo-header-glow"></div>
            <div class="dv-mo-icon">
                <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13" rx="1"/>
                    <path d="M16 8h4l3 3v5h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </div>
            <div class="dv-mo-header-body">
                <div class="dv-mo-eyebrow">Ship Action</div>
                <div class="dv-mo-title">Mark as Out for Delivery</div>
                <div class="dv-mo-sub">{{ $delivery->dr_number }} · {{ $delivery->store->store_name ?? '-' }}</div>
            </div>
            <button type="button" class="dv-mo-close" onclick="closeShipModal()" aria-label="Close">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- STEPS --}}
        <div class="dv-mo-steps">
            <div class="dv-mo-step done">
                <div class="dv-mo-step-dot">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                </div>
                <div class="dv-mo-step-label">Preparing</div>
            </div>
            <div class="dv-mo-step-line active"></div>
            <div class="dv-mo-step current">
                <div class="dv-mo-step-dot">2</div>
                <div class="dv-mo-step-label">Out for Delivery</div>
            </div>
            <div class="dv-mo-step-line"></div>
            <div class="dv-mo-step">
                <div class="dv-mo-step-dot">3</div>
                <div class="dv-mo-step-label">Confirmed</div>
            </div>
        </div>

        {{-- BODY --}}
        <div class="dv-mo-body">
            <div class="dv-mo-alert">
                <div class="dv-mo-alert-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4M12 8h.01"/>
                    </svg>
                </div>
                <div class="dv-mo-alert-text">
                    Mo-notify dayon ang store <strong>{{ $delivery->store->store_name ?? '-' }}</strong> sa ilang portal bell.
                </div>
            </div>

            <div class="dv-mo-facts">
                <div class="dv-mo-fact">
                    <div class="dv-mo-fact-icon">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    </div>
                    <div>
                        <div class="dv-mo-fact-label">Delivery Date</div>
                        <div class="dv-mo-fact-value">{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</div>
                    </div>
                </div>
                <div class="dv-mo-fact">
                    <div class="dv-mo-fact-icon">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <div class="dv-mo-fact-label">Items</div>
                        <div class="dv-mo-fact-value">{{ $delivery->items->count() }} product(s)</div>
                    </div>
                </div>
                <div class="dv-mo-fact">
                    <div class="dv-mo-fact-icon">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    </div>
                    <div>
                        <div class="dv-mo-fact-label">Total</div>
                        <div class="dv-mo-fact-value gold">&#8369;{{ number_format($delivery->total_amount, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="dv-mo-field">
                <div class="dv-mo-field-head">
                    <label class="dv-mo-field-label">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
                        Note sa store
                    </label>
                    <span class="dv-mo-field-optional">Optional</span>
                </div>
                <textarea id="shipNote" rows="2" class="dv-mo-textarea"
                          placeholder="e.g. ETA 2-3 hours, rider will call before arrival..."></textarea>
                <div class="dv-mo-field-hint">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4M12 8h.01"/>
                    </svg>
                    Makita ni sa store ang note sa ilang notification.
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="dv-mo-footer">
            <button type="button" class="dv-mo-btn dv-mo-btn-ghost" onclick="closeShipModal()">
                Cancel
            </button>
            <button type="submit" form="shipForm" class="dv-mo-btn dv-mo-btn-primary" onclick="return doShipSubmit(event);">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13" rx="1"/>
                    <path d="M16 8h4l3 3v5h-7V8z"/>
                </svg>
                Confirm Shipment
            </button>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('deliveries.out-for-delivery', $delivery->id) }}" id="shipForm" style="display:none;">
    @csrf
    <input type="hidden" name="ship_note" id="shipNoteInput">
</form>

@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════ */
/* DELIVERY SHOW — CLEAN & PREMIUM                 */
/* ═══════════════════════════════════════════════ */

/* ── BACK BUTTON ───────────────────────────────── */
.dv-back{
    display:inline-flex;align-items:center;gap:8px;
    padding:8px 14px 8px 11px;
    background:rgba(34,34,44,.55);
    border:1px solid rgba(255,255,255,.07);
    border-radius:10px;
    color:#a8a5a0;font-size:12.5px;font-weight:600;
    text-decoration:none;
    margin-bottom:16px;
    transition:all .2s cubic-bezier(.4,0,.2,1);
    backdrop-filter:blur(10px);
}
.dv-back:hover{
    background:rgba(201,169,97,.08);
    border-color:rgba(201,169,97,.28);
    color:#c9a961;
    transform:translateX(-3px);
}
.dv-back svg{transition:transform .2s}
.dv-back:hover svg{transform:translateX(-2px)}

/* ── HERO ──────────────────────────────────────── */
.dv-hero{
    position:relative;
    padding:28px 32px;
    background:#16161d;
    border:1px solid rgba(255,255,255,.06);
    border-radius:18px;
    margin-bottom:18px;
    overflow:hidden;
}
.dv-hero-grid{
    position:absolute;inset:0;
    background-image:
        linear-gradient(rgba(201,169,97,.04) 1px,transparent 1px),
        linear-gradient(90deg,rgba(201,169,97,.04) 1px,transparent 1px);
    background-size:40px 40px;
    mask-image:radial-gradient(circle at 100% 0%,black,transparent 70%);
    -webkit-mask-image:radial-gradient(circle at 100% 0%,black,transparent 70%);
    pointer-events:none;
}
.dv-hero-accent{
    position:absolute;left:0;top:0;bottom:0;
    width:4px;background:var(--status-color);
    opacity:.9;
}
.dv-hero-inner{
    position:relative;z-index:1;
    display:flex;align-items:center;justify-content:space-between;
    gap:28px;flex-wrap:wrap;
}
.dv-hero-left{
    display:flex;align-items:center;gap:18px;
    flex:1;min-width:280px;
}
.dv-hero-icon{
    width:64px;height:64px;
    border-radius:16px;
    display:grid;place-items:center;
    flex-shrink:0;
    border:1.5px solid;
}
.dv-hero-icon svg{width:28px;height:28px}
.dv-hero-info{flex:1;min-width:0}
.dv-hero-eyebrow{
    font-size:10.5px;font-weight:800;
    text-transform:uppercase;letter-spacing:.16em;
    margin-bottom:8px;
}
.dv-hero-title{
    font-size:28px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.03em;line-height:1.1;
    font-family:ui-monospace,SFMono-Regular,monospace;
    margin:0 0 10px;
}
.dv-hero-meta{
    display:flex;align-items:center;gap:8px;
    flex-wrap:wrap;font-size:12.5px;
}
.dv-hero-link{
    display:inline-flex;align-items:center;gap:4px;
    color:#c9a961;text-decoration:none;font-weight:700;
    transition:color .15s;
}
.dv-hero-link:hover{color:#d4b673}
.dv-hero-link svg{opacity:.6}
.dv-hero-dot{color:#52525b}
.dv-hero-code{
    font-family:ui-monospace,monospace;font-size:11.5px;
    color:#7a7a85;
}
.dv-hero-date{
    display:inline-flex;align-items:center;gap:5px;
    color:#d4d1cc;font-weight:600;
}
.dv-hero-date svg{color:#7a7a85}

.dv-hero-right{
    display:flex;align-items:center;gap:0;
    padding:14px 4px;
    border-top:1px solid rgba(255,255,255,.06);
    border-bottom:1px solid rgba(255,255,255,.06);
}
.dv-hero-stat{padding:0 22px;text-align:center;position:relative}
.dv-hero-stat + .dv-hero-stat::before{
    content:'';position:absolute;left:0;top:50%;
    width:1px;height:32px;background:rgba(255,255,255,.08);
    transform:translateY(-50%);
}
.dv-hero-stat-label{
    font-size:9.5px;font-weight:700;color:#7a7a85;
    text-transform:uppercase;letter-spacing:.12em;
    margin-bottom:6px;
}
.dv-hero-stat-value{
    font-size:18px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.02em;line-height:1;
    font-variant-numeric:tabular-nums;
}
.dv-hero-stat-value.green{color:#4ade80}
.dv-hero-stat-value.amber{color:#fbbf24}
.dv-hero-stat-divider{display:none}

/* ── GRID ──────────────────────────────────────── */
.dv-grid{
    display:grid;
    grid-template-columns:1.6fr 1fr;
    gap:16px;
}
.dv-col{display:flex;flex-direction:column;gap:16px}

/* ── CARD ──────────────────────────────────────── */
.dv-card{
    background:rgba(34,34,44,.5);
    backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,.06);
    border-radius:16px;
    padding:18px;
    transition:border-color .2s;
}
.dv-card:hover{border-color:rgba(201,169,97,.14)}

.dv-card-head{
    display:flex;align-items:center;gap:10px;
    padding-bottom:14px;margin-bottom:14px;
    border-bottom:1px dashed rgba(255,255,255,.06);
}
.dv-card-icon{
    width:32px;height:32px;border-radius:10px;
    background:rgba(201,169,97,.12);
    border:1px solid rgba(201,169,97,.22);
    color:#c9a961;
    display:grid;place-items:center;flex-shrink:0;
}
.dv-card-icon.green{
    background:rgba(34,197,94,.12);
    border-color:rgba(34,197,94,.22);
    color:#4ade80;
}
.dv-card-header-text{flex:1}
.dv-card-title{
    font-size:13.5px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.01em;
}
.dv-card-sub{
    font-size:11px;color:#7a7a85;font-weight:500;
    margin-top:2px;
}

/* ── ITEMS ─────────────────────────────────────── */
.dv-items{
    display:flex;flex-direction:column;
    gap:0;margin-bottom:14px;
}
.dv-item{
    display:flex;align-items:center;gap:12px;
    padding:11px 0;
    border-bottom:1px dashed rgba(255,255,255,.05);
}
.dv-item:first-child{padding-top:4px}
.dv-item:last-child{border-bottom:none;padding-bottom:8px}

.dv-item-thumb{
    width:42px;height:42px;border-radius:11px;
    background:rgba(201,169,97,.08);
    border:1px solid rgba(201,169,97,.15);
    display:grid;place-items:center;
    color:rgba(201,169,97,.6);
    overflow:hidden;flex-shrink:0;
}
.dv-item-thumb img{width:100%;height:100%;object-fit:cover}
.dv-item-info{flex:1;min-width:0}
.dv-item-name{
    font-size:13px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.01em;line-height:1.25;
    margin-bottom:4px;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.dv-item-meta{
    display:flex;align-items:center;gap:8px;
    font-size:10.5px;color:#7a7a85;
}
.dv-item-sku{
    font-family:ui-monospace,monospace;
    color:#6b6862;font-weight:700;
    padding:1px 6px;
    background:rgba(255,255,255,.04);
    border-radius:4px;
}
.dv-item-qty{color:#9a97a3;font-weight:600}
.dv-item-total{
    font-size:14px;font-weight:800;
    color:#c9a961;letter-spacing:-.01em;
    font-variant-numeric:tabular-nums;
    flex-shrink:0;
}

.dv-total-row{
    display:flex;justify-content:space-between;align-items:center;
    padding-top:14px;
    border-top:2px solid rgba(201,169,97,.2);
}
.dv-total-label{
    font-size:11.5px;font-weight:800;color:#7a7a85;
    text-transform:uppercase;letter-spacing:.1em;
}
.dv-total-value{
    font-size:22px;font-weight:800;
    color:#c9a961;letter-spacing:-.02em;
    font-variant-numeric:tabular-nums;
}

/* ── NOTES ─────────────────────────────────────── */
.dv-notes{
    font-size:12.5px;color:#a8a5a0;
    line-height:1.6;font-weight:500;
    padding:2px 0;
}

/* ── PAYMENT ROWS ──────────────────────────────── */
.dv-payment-rows{
    display:flex;flex-direction:column;
    padding-bottom:14px;
    border-bottom:1px dashed rgba(255,255,255,.06);
}
.dv-payment-row{
    display:flex;justify-content:space-between;align-items:center;
    padding:9px 0;font-size:12.5px;
}
.dv-payment-label{color:#9a97a3;font-weight:600}
.dv-payment-value{
    font-weight:800;color:#f5f3f0;
    letter-spacing:-.01em;
    font-variant-numeric:tabular-nums;
}
.dv-payment-value.green{color:#4ade80}

/* ── BALANCE TOTAL ─────────────────────────────── */
.dv-balance-total{
    display:flex;justify-content:space-between;align-items:center;
    padding:14px 16px;margin:14px 0;
    border-radius:12px;border:1px solid;
}
.dv-balance-total.is-owed{
    background:rgba(245,158,11,.08);
    border-color:rgba(245,158,11,.25);
}
.dv-balance-total.is-clear{
    background:rgba(34,197,94,.08);
    border-color:rgba(34,197,94,.25);
}
.dv-balance-total-label{
    display:inline-flex;align-items:center;gap:7px;
    font-size:11px;font-weight:800;
    text-transform:uppercase;letter-spacing:.08em;
}
.is-owed .dv-balance-total-label{color:#fbbf24}
.is-clear .dv-balance-total-label{color:#4ade80}
.dv-balance-total-value{
    font-size:18px;font-weight:800;
    letter-spacing:-.02em;
    font-variant-numeric:tabular-nums;
}
.is-owed .dv-balance-total-value{color:#fbbf24}
.is-clear .dv-balance-total-value{color:#4ade80}

/* ── ACTION BUTTONS ────────────────────────────── */
.dv-action-btn{
    display:flex;align-items:center;justify-content:center;gap:7px;
    width:100%;padding:12px 16px;margin-top:0;
    border-radius:10px;
    font-size:12.5px;font-weight:800;
    text-decoration:none;border:1px solid transparent;
    font-family:inherit;cursor:pointer;
    transition:all .18s cubic-bezier(.4,0,.2,1);
    letter-spacing:.01em;
}
.dv-action-primary{
    background:linear-gradient(135deg,#c9a961,#a9784a);
    color:#0f0f14;
    box-shadow:0 8px 20px -8px rgba(201,169,97,.55);
}
.dv-action-primary:hover{
    transform:translateY(-1px);
    box-shadow:0 12px 24px -8px rgba(201,169,97,.7);
    filter:brightness(1.05);
}
.dv-action-primary:active{transform:translateY(0)}

.dv-action-ship{
    background:linear-gradient(135deg,#f59e0b,#d97706);
    color:#fff;
    box-shadow:0 8px 20px -8px rgba(245,158,11,.6);
}
.dv-action-ship:hover{
    transform:translateY(-1px);
    filter:brightness(1.05);
    box-shadow:0 12px 24px -8px rgba(245,158,11,.75);
}
.dv-action-ship:active{transform:translateY(0)}

.dv-action-disabled{
    background:rgba(255,255,255,.04);
    border-color:rgba(255,255,255,.08);
    color:#7a7a85;
    cursor:not-allowed;
    opacity:.7;
}

.dv-action-ghost{
    background:rgba(255,255,255,.04);
    border-color:rgba(255,255,255,.08);
    color:#d4d1cc;
}
.dv-action-ghost:hover{
    background:rgba(255,255,255,.08);
    border-color:rgba(255,255,255,.15);
    color:#f5f3f0;
}

.dv-action-list{display:flex;flex-direction:column;gap:8px}
.dv-action-list form{margin:0}

.dv-action-hint{
    display:flex;align-items:flex-start;gap:8px;
    margin-top:12px;padding:11px 13px;
    background:rgba(245,158,11,.06);
    border:1px solid rgba(245,158,11,.2);
    border-radius:10px;
    font-size:11px;color:#d4a35a;
    line-height:1.5;font-weight:500;
}
.dv-action-hint svg{flex-shrink:0;margin-top:1px;color:#f59e0b}

/* ── DETAIL ROWS ───────────────────────────────── */
.dv-detail-rows{display:flex;flex-direction:column}
.dv-detail-row{
    display:flex;justify-content:space-between;align-items:center;
    gap:12px;padding:10px 0;font-size:12px;
    border-bottom:1px dashed rgba(255,255,255,.05);
}
.dv-detail-row:last-child{border-bottom:none}
.dv-detail-row:first-child{padding-top:2px}
.dv-detail-label{color:#7a7a85;font-weight:600}
.dv-detail-value{
    color:#f5f3f0;font-weight:700;
    text-align:right;
    font-variant-numeric:tabular-nums;
}

/* ── RESPONSIVE ────────────────────────────────── */
@media (max-width:1100px){
    .dv-grid{grid-template-columns:1fr}
}
@media (max-width:900px){
    .dv-hero{padding:24px}
    .dv-hero-inner{flex-direction:column;align-items:stretch}
    .dv-hero-right{
        width:100%;justify-content:space-around;
        padding:14px 0;
    }
    .dv-hero-stat{padding:0 12px;flex:1}
}
@media (max-width:480px){
    .dv-hero{padding:20px}
    .dv-hero-title{font-size:22px}
    .dv-hero-icon{width:52px;height:52px;border-radius:14px}
    .dv-hero-icon svg{width:22px;height:22px}
    .dv-hero-stat-val{font-size:16px}
    .dv-item-total{font-size:13px}
    .dv-total-value{font-size:18px}
}

/* ═══════════════════════════════════════════════ */
/* SHIP MODAL                                     */
/* ═══════════════════════════════════════════════ */
.dv-mo-backdrop{
    position:fixed;inset:0;
    background:rgba(0,0,0,.8);
    backdrop-filter:blur(12px);
    -webkit-backdrop-filter:blur(12px);
    display:none;align-items:center;justify-content:center;
    z-index:999999;padding:20px;
    animation:dvMoFade .22s ease-out;
}
.dv-mo-backdrop.active{display:flex}
@keyframes dvMoFade{from{opacity:0}to{opacity:1}}

.dv-mo{
    position:relative;
    background:#16161d;
    border:1px solid rgba(255,255,255,.08);
    border-radius:22px;
    width:100%;max-width:520px;
    max-height:92vh;overflow:hidden;
    box-shadow:0 40px 100px -30px rgba(0,0,0,.9),
               0 0 0 1px rgba(245,158,11,.08),
               0 0 80px -20px rgba(245,158,11,.15);
    display:flex;flex-direction:column;
    animation:dvMoIn .32s cubic-bezier(.34,1.4,.64,1);
}
@keyframes dvMoIn{
    from{transform:scale(.92) translateY(20px);opacity:0}
    to{transform:scale(1) translateY(0);opacity:1}
}

.dv-mo-header{
    position:relative;display:flex;align-items:center;gap:16px;
    padding:24px 24px 20px;
    border-bottom:1px solid rgba(255,255,255,.06);
    overflow:hidden;
}
.dv-mo-header-glow{
    position:absolute;top:-80px;right:-80px;
    width:240px;height:240px;
    background:radial-gradient(circle,rgba(245,158,11,.22),transparent 65%);
    pointer-events:none;
}
.dv-mo-icon{
    width:54px;height:54px;border-radius:15px;
    display:grid;place-items:center;flex-shrink:0;
    background:linear-gradient(135deg,rgba(245,158,11,.22),rgba(217,119,6,.12));
    color:#f59e0b;
    border:1px solid rgba(245,158,11,.35);
    box-shadow:0 8px 24px -8px rgba(245,158,11,.5);
    position:relative;z-index:1;
}
.dv-mo-header-body{flex:1;min-width:0;position:relative;z-index:1}
.dv-mo-eyebrow{
    font-size:9.5px;font-weight:800;color:#f59e0b;
    text-transform:uppercase;letter-spacing:.15em;
    margin-bottom:5px;
}
.dv-mo-title{
    font-size:18px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.02em;line-height:1.2;
    margin-bottom:4px;
}
.dv-mo-sub{
    font-size:11.5px;color:#7a7a85;
    font-family:ui-monospace,monospace;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.dv-mo-close{
    width:34px;height:34px;border-radius:10px;
    background:rgba(255,255,255,.04);
    border:1px solid rgba(255,255,255,.08);
    color:#a8a5a0;
    display:grid;place-items:center;
    cursor:pointer;flex-shrink:0;
    transition:all .15s;
    position:relative;z-index:1;
}
.dv-mo-close:hover{
    background:rgba(255,255,255,.08);
    color:#f5f3f0;
    transform:rotate(90deg);
}

/* STEPS */
.dv-mo-steps{
    display:flex;align-items:center;
    padding:18px 24px;
    background:rgba(0,0,0,.2);
    border-bottom:1px solid rgba(255,255,255,.05);
}
.dv-mo-step{
    display:flex;flex-direction:column;align-items:center;gap:6px;
    flex-shrink:0;opacity:.35;transition:opacity .3s;
}
.dv-mo-step.done{opacity:.7}
.dv-mo-step.current{opacity:1}
.dv-mo-step-dot{
    width:30px;height:30px;border-radius:50%;
    display:grid;place-items:center;
    font-size:11.5px;font-weight:800;
    font-family:ui-monospace,monospace;
    background:rgba(255,255,255,.05);
    color:#7a7a85;
    border:1.5px solid rgba(255,255,255,.1);
}
.dv-mo-step.done .dv-mo-step-dot{
    background:rgba(34,197,94,.15);
    color:#4ade80;
    border-color:rgba(34,197,94,.4);
}
.dv-mo-step.current .dv-mo-step-dot{
    background:linear-gradient(135deg,#f59e0b,#d97706);
    color:#fff;
    border-color:rgba(245,158,11,.6);
    box-shadow:0 0 0 4px rgba(245,158,11,.15);
    animation:dvMoPulse 2s ease-in-out infinite;
}
@keyframes dvMoPulse{
    0%,100%{box-shadow:0 0 0 4px rgba(245,158,11,.15)}
    50%{box-shadow:0 0 0 8px rgba(245,158,11,.05)}
}
.dv-mo-step-label{
    font-size:9.5px;font-weight:800;
    color:#a8a5a0;
    text-transform:uppercase;letter-spacing:.08em;
    text-align:center;white-space:nowrap;
}
.dv-mo-step.current .dv-mo-step-label{color:#f59e0b}
.dv-mo-step.done .dv-mo-step-label{color:#4ade80}
.dv-mo-step-line{
    flex:1;height:2px;background:rgba(255,255,255,.06);
    margin:0 10px;position:relative;top:-12px;
    border-radius:2px;
}
.dv-mo-step-line.active{
    background:linear-gradient(90deg,#22c55e,#f59e0b);
}

/* BODY */
.dv-mo-body{
    padding:20px 24px;
    overflow-y:auto;flex:1;
}
.dv-mo-alert{
    display:flex;align-items:flex-start;gap:12px;
    padding:14px 16px;
    background:rgba(59,130,246,.08);
    border:1px solid rgba(59,130,246,.25);
    border-radius:14px;
    margin-bottom:18px;
}
.dv-mo-alert-icon{
    width:30px;height:30px;border-radius:9px;
    display:grid;place-items:center;flex-shrink:0;
    background:rgba(59,130,246,.15);
    color:#3b82f6;
}
.dv-mo-alert-text{
    flex:1;font-size:12px;color:#bfdbfe;
    line-height:1.55;padding-top:5px;
}
.dv-mo-alert-text strong{color:#60a5fa;font-weight:800}

.dv-mo-facts{
    display:grid;grid-template-columns:1fr 1fr 1fr;
    gap:10px;margin-bottom:18px;
}
.dv-mo-fact{
    padding:12px;
    background:rgba(0,0,0,.22);
    border:1px solid rgba(255,255,255,.05);
    border-radius:12px;
    display:flex;flex-direction:column;gap:8px;
}
.dv-mo-fact-icon{
    width:26px;height:26px;border-radius:8px;
    display:grid;place-items:center;
    background:rgba(201,169,97,.12);
    color:#c9a961;
}
.dv-mo-fact-label{
    font-size:9px;font-weight:800;color:#7a7a85;
    text-transform:uppercase;letter-spacing:.08em;
    margin-bottom:3px;
}
.dv-mo-fact-value{
    font-size:12px;font-weight:800;color:#f5f3f0;
    font-variant-numeric:tabular-nums;
}
.dv-mo-fact-value.gold{color:#c9a961}

.dv-mo-field{margin-top:4px}
.dv-mo-field-head{
    display:flex;justify-content:space-between;align-items:center;
    margin-bottom:8px;
}
.dv-mo-field-label{
    display:inline-flex;align-items:center;gap:6px;
    font-size:11px;font-weight:800;color:#d4d1cc;
    text-transform:uppercase;letter-spacing:.08em;
}
.dv-mo-field-label svg{color:#c9a961}
.dv-mo-field-optional{
    font-size:9.5px;font-weight:700;color:#52525b;
    text-transform:uppercase;letter-spacing:.05em;
    padding:2px 8px;
    background:rgba(255,255,255,.04);
    border-radius:100px;
}
.dv-mo-textarea{
    width:100%;padding:12px 14px;
    background:rgba(0,0,0,.3);
    border:1px solid rgba(255,255,255,.08);
    border-radius:12px;
    color:#f5f3f0;font-size:13px;font-family:inherit;
    resize:vertical;min-height:68px;
    transition:all .15s;line-height:1.5;
    box-sizing:border-box;
}
.dv-mo-textarea:focus{
    outline:none;
    border-color:rgba(245,158,11,.5);
    background:rgba(245,158,11,.04);
    box-shadow:0 0 0 4px rgba(245,158,11,.1);
}
.dv-mo-textarea::placeholder{color:#52525b}
.dv-mo-field-hint{
    display:inline-flex;align-items:center;gap:5px;
    margin-top:8px;font-size:10.5px;color:#7a7a85;
}
.dv-mo-field-hint svg{color:#52525b;flex-shrink:0}

.dv-mo-footer{
    display:flex;gap:10px;
    padding:18px 24px 22px;
    border-top:1px solid rgba(255,255,255,.05);
    background:rgba(0,0,0,.12);
}
.dv-mo-btn{
    display:inline-flex;align-items:center;justify-content:center;gap:7px;
    padding:13px 18px;border-radius:12px;
    font-size:12.5px;font-weight:800;
    border:1px solid;cursor:pointer;font-family:inherit;
    transition:all .18s cubic-bezier(.4,0,.2,1);
    min-height:48px;letter-spacing:.01em;
}
.dv-mo-btn-ghost{
    flex:0 0 auto;padding:13px 20px;
    background:rgba(255,255,255,.04);
    border-color:rgba(255,255,255,.1);
    color:#d4d1cc;
}
.dv-mo-btn-ghost:hover{
    background:rgba(255,255,255,.08);
    color:#f5f3f0;
    border-color:rgba(255,255,255,.18);
}
.dv-mo-btn-ghost:active{transform:scale(.97)}
.dv-mo-btn-primary{
    flex:1;
    background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);
    border-color:transparent;color:#fff;
    box-shadow:0 8px 24px -8px rgba(245,158,11,.7),
               inset 0 1px 0 rgba(255,255,255,.2);
}
.dv-mo-btn-primary:hover{
    transform:translateY(-2px);
    box-shadow:0 14px 32px -8px rgba(245,158,11,.85),
               inset 0 1px 0 rgba(255,255,255,.2);
}
.dv-mo-btn-primary:active{transform:scale(.98) translateY(0)}

/* TOAST */
.dv-toast{
    position:fixed;top:24px;right:24px;
    display:flex;align-items:center;gap:12px;
    padding:14px 18px;
    background:linear-gradient(135deg,rgba(34,197,94,.95),rgba(22,163,74,.95));
    border:1px solid rgba(34,197,94,.4);
    border-radius:14px;
    box-shadow:0 20px 40px -12px rgba(34,197,94,.5);
    z-index:9999999;color:#fff;
    font-size:13px;font-weight:700;
    animation:dvToastIn .35s cubic-bezier(.34,1.56,.64,1);
    max-width:360px;
}
@keyframes dvToastIn{
    from{transform:translateX(calc(100% + 24px));opacity:0}
    to{transform:translateX(0);opacity:1}
}
.dv-toast-icon{
    width:32px;height:32px;border-radius:10px;
    background:rgba(255,255,255,.2);
    display:grid;place-items:center;flex-shrink:0;
}
.dv-toast-body{flex:1;min-width:0}
.dv-toast-title{font-size:12.5px;font-weight:800;margin-bottom:2px}
.dv-toast-desc{font-size:11px;opacity:.9;line-height:1.4}

@media (max-width:700px){
    .dv-mo-backdrop{padding:0;align-items:flex-end}
    .dv-mo{
        border-radius:22px 22px 0 0;
        max-width:100%;max-height:95vh;
        animation:dvMoUp .35s cubic-bezier(.34,1.2,.64,1);
    }
    @keyframes dvMoUp{
        from{transform:translateY(100%);opacity:0}
        to{transform:translateY(0);opacity:1}
    }
    .dv-mo-header{padding:20px 20px 16px}
    .dv-mo-body{padding:18px 20px}
    .dv-mo-footer{padding:16px 20px 20px}
    .dv-mo-facts{grid-template-columns:1fr 1fr}
    .dv-mo-facts .dv-mo-fact:last-child{grid-column:span 2}
    .dv-toast{top:12px;right:12px;left:12px;max-width:none}
}
@media (max-width:400px){
    .dv-mo-facts{grid-template-columns:1fr}
    .dv-mo-facts .dv-mo-fact:last-child{grid-column:span 1}
}
</style>
@endpush

@push('scripts')
<script>
    function openShipModal() {
        var modal = document.getElementById('shipModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        setTimeout(function() {
            var ta = document.getElementById('shipNote');
            if (ta) ta.focus();
        }, 150);
    }

    function closeShipModal() {
        var modal = document.getElementById('shipModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('shipForm');
        if (form) {
            form.addEventListener('submit', function() {
                var note = document.getElementById('shipNote').value;
                document.getElementById('shipNoteInput').value = note;
            });
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeShipModal();
    });

    window.doShipSubmit = function(e) {
        try {
            var note = document.getElementById('shipNote');
            var input = document.getElementById('shipNoteInput');
            if (note && input) input.value = note.value || '';
        } catch (err) {}

        var form = document.getElementById('shipForm');
        if (!form) {
            alert('Form not found — please refresh the page.');
            return false;
        }

        if (e) e.preventDefault();

        try {
            form.submit();
        } catch (err) {
            alert('Submit failed: ' + err.message);
        }
        return false;
    };

    @if(session('success'))
        (function() {
            var toast = document.createElement('div');
            toast.className = 'dv-toast';
            toast.innerHTML = '<div class="dv-toast-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></div>' +
                '<div class="dv-toast-body">' +
                    '<div class="dv-toast-title">Success!</div>' +
                    '<div class="dv-toast-desc">{{ session("success") }}</div>' +
                '</div>';
            document.body.appendChild(toast);
            setTimeout(function() {
                toast.style.transition = 'all 0.3s';
                toast.style.transform = 'translateX(calc(100% + 24px))';
                toast.style.opacity = '0';
                setTimeout(function() { toast.remove(); }, 300);
            }, 4000);
        })();
    @endif
</script>
@endpush