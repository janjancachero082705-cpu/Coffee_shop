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
            'bg'    => 'rgba(34,197,94,0.12)',
            'icon'  => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>',
        ];
    } elseif ($isOut) {
        $statusConfig = [
            'label' => 'Out for Delivery',
            'color' => '#3b82f6',
            'bg'    => 'rgba(59,130,246,0.12)',
            'icon'  => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
        ];
    } else {
        $statusConfig = [
            'label' => 'Pending',
            'color' => '#f59e0b',
            'bg'    => 'rgba(245,158,11,0.12)',
            'icon'  => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
        ];
    }

    $canRecordPayment = ($delivery->balance > 0) && $isConfirmed;
@endphp

{{-- ========== HERO ========== --}}
<div class="dv-hero" style="--status-color: {{ $statusConfig['color'] }};">
    <div class="dv-hero-icon" style="background: {{ $statusConfig['bg'] }}; color: {{ $statusConfig['color'] }};">
        {!! $statusConfig['icon'] !!}
    </div>

    <div class="dv-hero-content">
        <div class="dv-hero-label">{{ $statusConfig['label'] }}</div>
        <div class="dv-hero-title">{{ $delivery->dr_number }}</div>
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

    <div class="dv-hero-stats">
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

{{-- ========== GRID ========== --}}
<div class="dv-grid">

    {{-- LEFT: Items + Notes --}}
    <div>
        <div class="dv-card">
            <div class="dv-card-head">
                <div class="dv-card-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
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
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            @endif
                        </div>
                        <div class="dv-item-info">
                            <div class="dv-item-name">{{ $item->product->name ?? '-' }}</div>
                            <div class="dv-item-meta">
                                <span class="dv-item-sku">{{ $item->product->sku ?? '' }}</span>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 7h16M4 12h16M4 17h10"/>
                        </svg>
                    </div>
                    <div class="dv-card-title">Notes</div>
                </div>
                <div class="dv-notes">{{ $delivery->notes }}</div>
            </div>
        @endif
    </div>

    {{-- RIGHT: Payment + Meta --}}
    <div>
        <div class="dv-card">
            <div class="dv-card-head">
                <div class="dv-card-icon green">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                    <span class="dv-payment-value green">- &#8369;{{ number_format($delivery->amount_paid, 2) }}</span>
                </div>
                <div class="dv-payment-row dv-payment-row-total">
                    <span class="dv-payment-label">Balance</span>
                    <span class="dv-payment-value {{ $delivery->balance > 0 ? 'amber' : 'green' }}" style="font-size:16px;">&#8369;{{ number_format($delivery->balance, 2) }}</span>
                </div>
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
                    {{-- ========== SHIP BUTTON (Out for Delivery) ========== --}}
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
                    {{-- Waiting for customer confirmation state --}}
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
                        Dili pa ma-record ang payment. Kinahanglan mo-confirm una ang customer nga nadawat niya ang products.
                    </div>
                @endif
            @else
                <div class="dv-paid-badge">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M5 12l5 5L20 7"/>
                    </svg>
                    Fully Paid
                </div>
            @endif
        </div>

        <div class="dv-card">
            <div class="dv-card-head">
                <div class="dv-card-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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

        <div class="dv-card">
            <div class="dv-card-head">
                <div class="dv-card-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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


{{-- ========== SHIP DELIVERY — PREMIUM MODAL ========== --}}
<div id="shipModal" class="dv-mo-backdrop" onclick="if(event.target === this) closeShipModal()">
    <div class="dv-mo">

        {{-- HEADER --}}
        <div class="dv-mo-header">
            <div class="dv-mo-header-glow"></div>
            <div class="dv-mo-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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

        {{-- PROGRESS STEPS --}}
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

            {{-- INFO ALERT --}}
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

            {{-- QUICK FACTS --}}
            <div class="dv-mo-facts">
                <div class="dv-mo-fact">
                    <div class="dv-mo-fact-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    </div>
                    <div class="dv-mo-fact-body">
                        <div class="dv-mo-fact-label">Delivery Date</div>
                        <div class="dv-mo-fact-value">{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</div>
                    </div>
                </div>
                <div class="dv-mo-fact">
                    <div class="dv-mo-fact-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div class="dv-mo-fact-body">
                        <div class="dv-mo-fact-label">Items</div>
                        <div class="dv-mo-fact-value">{{ $delivery->items->count() }} product(s)</div>
                    </div>
                </div>
                <div class="dv-mo-fact">
                    <div class="dv-mo-fact-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    </div>
                    <div class="dv-mo-fact-body">
                        <div class="dv-mo-fact-label">Total</div>
                        <div class="dv-mo-fact-value gold">&#8369;{{ number_format($delivery->total_amount, 2) }}</div>
                    </div>
                </div>
            </div>

            {{-- NOTE TO STORE --}}
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
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                Cancel
            </button>
            <button type="submit" form="shipForm" class="dv-mo-btn dv-mo-btn-primary" onclick="return doShipSubmit(event);">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13" rx="1"/>
                    <path d="M16 8h4l3 3v5h-7V8z"/>
                </svg>
                Mark as Out for Delivery
            </button>
        </div>
    </div>
</div>

{{-- SHIP FORM (outside modal for reliability) --}}
<form method="POST" action="{{ route('deliveries.out-for-delivery', $delivery->id) }}" id="shipForm" style="display:none;">
    @csrf
    <input type="hidden" name="ship_note" id="shipNoteInput">
</form>

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

    // Copy note value to hidden input before submit
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('shipForm');
        if (form) {
            form.addEventListener('submit', function() {
                var note = document.getElementById('shipNote').value;
                document.getElementById('shipNoteInput').value = note;
            });
        }
    });

    // Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeShipModal();
    });

    // ========== SHOW SUCCESS TOAST (kung naay session flash) ==========
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

<script>
    window.doShipSubmit = function(e) {
        console.log('[Ship] Submit clicked');

        // Copy note value
        try {
            var note = document.getElementById('shipNote');
            var input = document.getElementById('shipNoteInput');
            if (note && input) {
                input.value = note.value || '';
            }
        } catch (err) {
            console.warn('[Ship] Note copy failed:', err);
        }

        // Find form
        var form = document.getElementById('shipForm');
        if (!form) {
            console.error('[Ship] Form #shipForm not found!');
            alert('Form not found — please refresh the page.');
            return false;
        }

        console.log('[Ship] Submitting to:', form.action);

        // Prevent default (browser button->form binding)
        if (e) e.preventDefault();

        // Submit manually
        try {
            form.submit();
        } catch (err) {
            console.error('[Ship] Submit error:', err);
            alert('Submit failed: ' + err.message);
        }

        return false;
    };
</script>
@endsection

@push('styles')
<style>
    /* ============================================================
       DELIVERY SHOW — PROFESSIONAL REDESIGN
       ============================================================ */

    /* ========== HERO ========== */
    .dv-hero {
        position: relative;
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 22px 26px;
        margin-bottom: 18px;
        background: linear-gradient(135deg, rgba(30, 26, 22, 0.85) 0%, rgba(21, 18, 15, 0.9) 100%);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-left: 3px solid var(--status-color);
        border-radius: 18px;
        overflow: hidden;
    }
    .dv-hero::before {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 260px; height: 260px;
        background: radial-gradient(circle, var(--status-color), transparent 70%);
        opacity: 0.15;
        pointer-events: none;
    }
    .dv-hero-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        border: 1px solid rgba(255, 255, 255, 0.08);
        position: relative;
        z-index: 1;
    }
    .dv-hero-icon svg { width: 26px; height: 26px; }

    .dv-hero-content {
        flex: 1;
        min-width: 0;
        position: relative;
        z-index: 1;
    }
    .dv-hero-label {
        font-size: 10px;
        font-weight: 800;
        color: var(--status-color);
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 6px;
    }
    .dv-hero-title {
        font-size: 22px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.02em;
        font-family: ui-monospace, monospace;
        line-height: 1.1;
        margin-bottom: 10px;
    }
    .dv-hero-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        font-size: 12px;
    }
    .dv-hero-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #c9a961;
        text-decoration: none;
        font-weight: 700;
        transition: color 0.15s;
    }
    .dv-hero-link:hover { color: #d4b673; }
    .dv-hero-link svg { opacity: 0.6; }
    .dv-hero-dot { color: #52525b; }
    .dv-hero-code {
        font-family: ui-monospace, monospace;
        font-size: 11px;
        color: #71717a;
    }
    .dv-hero-date {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #d4d4d8;
        font-weight: 600;
    }
    .dv-hero-date svg { color: #71717a; }

    .dv-hero-stats {
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        z-index: 1;
        flex-shrink: 0;
        padding-left: 20px;
        border-left: 1px solid rgba(255, 255, 255, 0.06);
    }
    .dv-hero-stat { text-align: right; }
    .dv-hero-stat-label {
        font-size: 9.5px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 5px;
    }
    .dv-hero-stat-value {
        font-size: 17px;
        font-weight: 800;
        color: #fafafa;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
    .dv-hero-stat-value.green { color: #22c55e; }
    .dv-hero-stat-value.amber { color: #f59e0b; }
    .dv-hero-stat-divider {
        width: 1px;
        height: 32px;
        background: rgba(255, 255, 255, 0.06);
    }

    /* ========== GRID ========== */
    .dv-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 16px;
    }

    /* ========== CARD ========== */
    .dv-card {
        background: linear-gradient(165deg, rgba(30, 26, 22, 0.9), rgba(21, 18, 15, 0.9));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 14px;
    }
    .dv-card:last-child { margin-bottom: 0; }

    .dv-card-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 12px;
        margin-bottom: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .dv-card-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: rgba(201, 169, 97, 0.12);
        color: #c9a961;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .dv-card-icon.green {
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
    }
    .dv-card-title {
        font-size: 13.5px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.01em;
    }
    .dv-card-sub {
        font-size: 10.5px;
        color: #71717a;
        margin-top: 2px;
    }

    /* ========== ITEMS ========== */
    .dv-items {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 14px;
    }
    .dv-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .dv-item:last-child { border-bottom: none; padding-bottom: 0; }
    .dv-item:first-child { padding-top: 0; }

    .dv-item-thumb {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(169, 120, 74, 0.1);
        display: grid;
        place-items: center;
        color: rgba(201, 169, 97, 0.5);
        overflow: hidden;
        flex-shrink: 0;
    }
    .dv-item-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .dv-item-info { flex: 1; min-width: 0; }
    .dv-item-name {
        font-size: 12.5px;
        font-weight: 700;
        color: #fafafa;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 3px;
    }
    .dv-item-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 10.5px;
        color: #71717a;
    }
    .dv-item-sku {
        font-family: ui-monospace, monospace;
        color: #52525b;
    }
    .dv-item-qty { color: #8a8378; }
    .dv-item-total {
        font-size: 13.5px;
        font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
        flex-shrink: 0;
    }

    .dv-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 14px;
        border-top: 2px solid rgba(201, 169, 97, 0.25);
    }
    .dv-total-label {
        font-size: 12.5px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .dv-total-value {
        font-size: 20px;
        font-weight: 800;
        color: #c9a961;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }

    /* ========== NOTES ========== */
    .dv-notes {
        font-size: 12.5px;
        color: #a1a1aa;
        line-height: 1.6;
    }

    /* ========== PAYMENT ROWS ========== */
    .dv-payment-rows {
        display: flex;
        flex-direction: column;
    }
    .dv-payment-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        font-size: 12.5px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .dv-payment-row:last-child { border-bottom: none; }
    .dv-payment-label { color: #71717a; font-weight: 600; }
    .dv-payment-value {
        font-weight: 800;
        color: #fafafa;
        font-variant-numeric: tabular-nums;
    }
    .dv-payment-value.green { color: #22c55e; }
    .dv-payment-value.amber { color: #f59e0b; }
    .dv-payment-row-total {
        margin-top: 6px;
        padding-top: 14px;
        border-top: 1px solid rgba(201, 169, 97, 0.25) !important;
    }

    /* ========== ACTION BUTTONS ========== */
    .dv-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: 100%;
        padding: 11px 16px;
        margin-top: 14px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.15s;
        border: 1px solid;
        font-family: inherit;
        cursor: pointer;
    }
    .dv-action-primary {
        background: linear-gradient(135deg, #c9a961, #b8944d);
        border-color: transparent;
        color: #0f0f14;
        box-shadow: 0 4px 12px -4px rgba(201, 169, 97, 0.5);
    }
    .dv-action-primary:hover {
        background: linear-gradient(135deg, #d4b672, #c9a961);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px -4px rgba(201, 169, 97, 0.6);
    }
    .dv-action-disabled {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
        color: #71717a;
        cursor: not-allowed;
        opacity: 0.7;
    }
    .dv-action-ghost {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
        color: #d4d4d8;
        margin-top: 0;
    }
    .dv-action-ghost:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.15);
        color: #fafafa;
    }
    .dv-action-danger {
        background: rgba(239, 68, 68, 0.08);
        border-color: rgba(239, 68, 68, 0.25);
        color: #f87171;
        margin-top: 0;
    }
    .dv-action-danger:hover {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.4);
        color: #fca5a5;
    }

    .dv-action-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .dv-action-list form { margin: 0; }

    .dv-action-hint {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin-top: 10px;
        padding: 10px 12px;
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.22);
        border-radius: 10px;
        font-size: 11px;
        color: #d4a35a;
        line-height: 1.5;
    }
    .dv-action-hint svg {
        flex-shrink: 0;
        margin-top: 1px;
        color: #f59e0b;
    }

    .dv-paid-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        margin-top: 14px;
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.25);
        border-radius: 10px;
        color: #22c55e;
        font-weight: 800;
        font-size: 12.5px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* ========== DETAIL ROWS ========== */
    .dv-detail-rows {
        display: flex;
        flex-direction: column;
    }
    .dv-detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        font-size: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }
    .dv-detail-row:last-child { border-bottom: none; }
    .dv-detail-label {
        color: #71717a;
        font-weight: 600;
    }
    .dv-detail-value {
        color: #fafafa;
        font-weight: 700;
        text-align: right;
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 1100px) {
        .dv-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 700px) {
        .dv-hero {
            flex-direction: column;
            align-items: flex-start;
            padding: 18px 20px;
        }
        .dv-hero-stats {
            padding-left: 0;
            padding-top: 16px;
            border-left: none;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            width: 100%;
            justify-content: space-between;
            gap: 10px;
        }
        .dv-hero-stat { text-align: left; }
        .dv-hero-stat-value { font-size: 15px; }
        .dv-hero-title { font-size: 18px; }
    }
    @media (max-width: 480px) {
        .dv-hero-stat-divider { display: none; }
        .dv-hero-stats { flex-wrap: wrap; }
    }

    /* ========== SHIP BUTTON (Out for Delivery) ========== */
    .dv-action-ship {
        background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        border-color: transparent !important;
        color: #fff !important;
        box-shadow: 0 4px 14px -4px rgba(245, 158, 11, 0.6) !important;
    }
    .dv-action-ship:hover {
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        transform: translateY(-1px);
        box-shadow: 0 8px 22px -6px rgba(245, 158, 11, 0.8) !important;
    }

    /* ============================================================
       PREMIUM SHIP MODAL
       ============================================================ */
    .dv-mo-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        padding: 20px;
        animation: dvMoFade 0.22s ease-out;
    }
    .dv-mo-backdrop.active { display: flex; }
    @keyframes dvMoFade {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .dv-mo {
        position: relative;
        background: linear-gradient(180deg, #1e1a16 0%, #15120f 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        width: 100%;
        max-width: 520px;
        max-height: 92vh;
        overflow: hidden;
        box-shadow:
            0 40px 100px -30px rgba(0, 0, 0, 0.9),
            0 0 0 1px rgba(245, 158, 11, 0.08),
            0 0 80px -20px rgba(245, 158, 11, 0.15);
        display: flex;
        flex-direction: column;
        animation: dvMoIn 0.32s cubic-bezier(0.34, 1.4, 0.64, 1);
    }
    @keyframes dvMoIn {
        from { transform: scale(0.92) translateY(20px); opacity: 0; }
        to   { transform: scale(1) translateY(0); opacity: 1; }
    }

    /* HEADER */
    .dv-mo-header {
        position: relative;
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 24px 24px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        overflow: hidden;
    }
    .dv-mo-header-glow {
        position: absolute;
        top: -80px; right: -80px;
        width: 240px; height: 240px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.25), transparent 65%);
        pointer-events: none;
    }
    .dv-mo-icon {
        width: 56px; height: 56px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.25), rgba(217, 119, 6, 0.15));
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.35);
        box-shadow: 0 8px 24px -8px rgba(245, 158, 11, 0.5);
        position: relative;
        z-index: 1;
    }
    .dv-mo-header-body {
        flex: 1;
        min-width: 0;
        position: relative;
        z-index: 1;
    }
    .dv-mo-eyebrow {
        font-size: 9.5px;
        font-weight: 800;
        color: #f59e0b;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        margin-bottom: 5px;
    }
    .dv-mo-title {
        font-size: 18px;
        font-weight: 800;
        color: #fafafa;
        letter-spacing: -0.02em;
        line-height: 1.2;
        margin-bottom: 4px;
    }
    .dv-mo-sub {
        font-size: 11.5px;
        color: #71717a;
        font-family: ui-monospace, monospace;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .dv-mo-close {
        width: 34px; height: 34px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #a1a1aa;
        display: grid;
        place-items: center;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.15s;
        position: relative;
        z-index: 1;
    }
    .dv-mo-close:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fafafa;
        transform: rotate(90deg);
    }

    /* STEPS */
    .dv-mo-steps {
        display: flex;
        align-items: center;
        padding: 18px 24px;
        background: rgba(0, 0, 0, 0.25);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .dv-mo-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
        opacity: 0.35;
        transition: opacity 0.3s;
    }
    .dv-mo-step.done { opacity: 0.7; }
    .dv-mo-step.current { opacity: 1; }
    .dv-mo-step-dot {
        width: 30px; height: 30px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 11.5px;
        font-weight: 800;
        font-family: ui-monospace, monospace;
        background: rgba(255, 255, 255, 0.05);
        color: #71717a;
        border: 1.5px solid rgba(255, 255, 255, 0.1);
    }
    .dv-mo-step.done .dv-mo-step-dot {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
        border-color: rgba(34, 197, 94, 0.4);
    }
    .dv-mo-step.current .dv-mo-step-dot {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
        border-color: rgba(245, 158, 11, 0.6);
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.15);
        animation: dvMoPulse 2s ease-in-out infinite;
    }
    @keyframes dvMoPulse {
        0%, 100% { box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.15); }
        50%      { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0.05); }
    }
    .dv-mo-step-label {
        font-size: 9.5px;
        font-weight: 800;
        color: #a1a1aa;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        text-align: center;
        white-space: nowrap;
    }
    .dv-mo-step.current .dv-mo-step-label { color: #f59e0b; }
    .dv-mo-step.done .dv-mo-step-label { color: #22c55e; }
    .dv-mo-step-line {
        flex: 1;
        height: 2px;
        background: rgba(255, 255, 255, 0.06);
        margin: 0 10px;
        position: relative;
        top: -12px;
        border-radius: 2px;
    }
    .dv-mo-step-line.active {
        background: linear-gradient(90deg, #22c55e, #f59e0b);
    }

    /* BODY */
    .dv-mo-body {
        padding: 20px 24px;
        overflow-y: auto;
        flex: 1;
    }

    /* ALERT */
    .dv-mo-alert {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.02));
        border: 1px solid rgba(59, 130, 246, 0.25);
        border-radius: 14px;
        margin-bottom: 18px;
    }
    .dv-mo-alert-icon {
        width: 32px; height: 32px;
        border-radius: 9px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        background: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
    }
    .dv-mo-alert-text {
        flex: 1;
        font-size: 12px;
        color: #bfdbfe;
        line-height: 1.55;
        padding-top: 6px;
    }
    .dv-mo-alert-text strong {
        color: #60a5fa;
        font-weight: 800;
    }

    /* FACTS */
    .dv-mo-facts {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 10px;
        margin-bottom: 18px;
    }
    .dv-mo-fact {
        padding: 12px;
        background: rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .dv-mo-fact-icon {
        width: 26px; height: 26px;
        border-radius: 8px;
        display: grid;
        place-items: center;
        background: rgba(201, 169, 97, 0.12);
        color: #c9a961;
    }
    .dv-mo-fact-label {
        font-size: 9px;
        font-weight: 800;
        color: #71717a;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 3px;
    }
    .dv-mo-fact-value {
        font-size: 12px;
        font-weight: 800;
        color: #fafafa;
        font-variant-numeric: tabular-nums;
    }
    .dv-mo-fact-value.gold { color: #c9a961; }

    /* FIELD */
    .dv-mo-field {
        margin-top: 4px;
    }
    .dv-mo-field-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .dv-mo-field-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 800;
        color: #d4d4d8;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .dv-mo-field-label svg { color: #c9a961; }
    .dv-mo-field-optional {
        font-size: 9.5px;
        font-weight: 700;
        color: #52525b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 2px 8px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 100px;
    }
    .dv-mo-textarea {
        width: 100%;
        padding: 12px 14px;
        background: rgba(0, 0, 0, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        color: #fafafa;
        font-size: 13px;
        font-family: inherit;
        resize: vertical;
        min-height: 68px;
        transition: all 0.15s;
        line-height: 1.5;
    }
    .dv-mo-textarea:focus {
        outline: none;
        border-color: rgba(245, 158, 11, 0.5);
        background: rgba(245, 158, 11, 0.04);
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
    }
    .dv-mo-textarea::placeholder { color: #52525b; }
    .dv-mo-field-hint {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 8px;
        font-size: 10.5px;
        color: #71717a;
    }
    .dv-mo-field-hint svg { color: #52525b; flex-shrink: 0; }

    /* FOOTER */
    .dv-mo-footer {
        display: flex;
        gap: 10px;
        padding: 18px 24px 22px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        background: rgba(0, 0, 0, 0.15);
    }
    .dv-mo-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 13px 18px;
        border-radius: 12px;
        font-size: 12.5px;
        font-weight: 800;
        border: 1px solid;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        min-height: 48px;
        letter-spacing: 0.01em;
    }
    .dv-mo-btn-ghost {
        flex: 0 0 auto;
        padding: 13px 20px;
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.1);
        color: #d4d4d8;
    }
    .dv-mo-btn-ghost:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fafafa;
        border-color: rgba(255, 255, 255, 0.18);
    }
    .dv-mo-btn-ghost:active { transform: scale(0.97); }
    .dv-mo-btn-primary {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-color: transparent;
        color: #fff;
        box-shadow:
            0 8px 24px -8px rgba(245, 158, 11, 0.7),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
    }
    .dv-mo-btn-primary::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent 50%);
        opacity: 0;
        transition: opacity 0.2s;
    }
    .dv-mo-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow:
            0 14px 32px -8px rgba(245, 158, 11, 0.85),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }
    .dv-mo-btn-primary:hover::before { opacity: 1; }
    .dv-mo-btn-primary:active { transform: scale(0.98) translateY(0); }

    /* SUCCESS TOAST */
    .dv-toast {
        position: fixed;
        top: 24px;
        right: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.95), rgba(22, 163, 74, 0.95));
        border: 1px solid rgba(34, 197, 94, 0.4);
        border-radius: 14px;
        box-shadow: 0 20px 40px -12px rgba(34, 197, 94, 0.5);
        z-index: 9999999;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        animation: dvToastIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        max-width: 360px;
    }
    @keyframes dvToastIn {
        from { transform: translateX(calc(100% + 24px)); opacity: 0; }
        to   { transform: translateX(0); opacity: 1; }
    }
    .dv-toast-icon {
        width: 32px; height: 32px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.2);
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }
    .dv-toast-body { flex: 1; min-width: 0; }
    .dv-toast-title {
        font-size: 12.5px;
        font-weight: 800;
        margin-bottom: 2px;
    }
    .dv-toast-desc {
        font-size: 11px;
        opacity: 0.9;
        line-height: 1.4;
    }

    /* RESPONSIVE */
    @media (max-width: 700px) {
        .dv-mo-backdrop { padding: 0; align-items: flex-end; }
        .dv-mo {
            border-radius: 24px 24px 0 0;
            max-width: 100%;
            max-height: 95vh;
            animation: dvMoUp 0.35s cubic-bezier(0.34, 1.2, 0.64, 1);
        }
        @keyframes dvMoUp {
            from { transform: translateY(100%); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }
        .dv-mo-header { padding: 20px 20px 16px; }
        .dv-mo-body { padding: 18px 20px; }
        .dv-mo-footer { padding: 16px 20px 20px; }
        .dv-mo-facts { grid-template-columns: 1fr 1fr; }
        .dv-mo-facts .dv-mo-fact:last-child { grid-column: span 2; }
        .dv-mo-step-label { font-size: 9px; }
        .dv-mo-btn-ghost { padding: 13px 16px; font-size: 12px; }
        .dv-mo-btn-primary { font-size: 12px; padding: 13px 14px; }
        .dv-toast { top: 12px; right: 12px; left: 12px; max-width: none; }
    }
    @media (max-width: 400px) {
        .dv-mo-facts { grid-template-columns: 1fr; }
        .dv-mo-facts .dv-mo-fact:last-child { grid-column: span 1; }
    }
</style>
@endpush