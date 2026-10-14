@extends('layouts.admin')

@section('title', 'Edit Delivery')
@section('subtitle', $delivery->dr_number)

@section('actions')
    <a href="{{ route('deliveries.show', $delivery) }}" class="btn btn-ghost btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back
    </a>
@endsection

@section('content')

{{-- BACK BUTTON --}}
<a href="{{ route('deliveries.show', $delivery) }}" class="ed-back">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
    <span>Back to Delivery</span>
</a>

{{-- HERO --}}
<div class="ed-hero">
    <div class="ed-hero-grid"></div>

    <div class="ed-hero-inner">
        <div class="ed-hero-left">
            <div class="ed-hero-eyebrow">Edit Delivery</div>
            <h1 class="ed-hero-title">{{ $delivery->dr_number }}</h1>
            <p class="ed-hero-sub">Update dates, status, and notes</p>
        </div>

        <div class="ed-hero-icon">
            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('deliveries.update', $delivery) }}" class="ed-form">
    @csrf @method('PUT')

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- LOCKED INFO                                     --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="ed-card">
        <div class="ed-card-head">
            <div class="ed-card-icon ed-card-icon-muted">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
            </div>
            <div class="ed-card-header-text">
                <div class="ed-card-title">Locked Information</div>
                <div class="ed-card-sub">Dili na mabag-o human sa creation</div>
            </div>
        </div>

        <div class="ed-locked-grid">
            <div class="ed-locked-field">
                <div class="ed-locked-label">Store</div>
                <div class="ed-locked-value">{{ $delivery->store->store_name ?? '—' }}</div>
            </div>
            <div class="ed-locked-field">
                <div class="ed-locked-label">DR Number</div>
                <div class="ed-locked-value ed-locked-mono">{{ $delivery->dr_number }}</div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- EDITABLE INFO                                   --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="ed-card">
        <div class="ed-card-head">
            <div class="ed-card-icon">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 16v-4M12 8h.01"/>
                </svg>
            </div>
            <div class="ed-card-header-text">
                <div class="ed-card-title">Delivery Information</div>
                <div class="ed-card-sub">Update dates, status, and notes</div>
            </div>
        </div>

        <div class="ed-form-grid">
            <div class="ed-field">
                <label class="ed-label">Delivery Date <span class="ed-req">*</span></label>
                <input type="date" name="delivery_date" value="{{ old('delivery_date', $delivery->delivery_date) }}" class="ed-input" required>
                @error('delivery_date')<div class="ed-error">{{ $message }}</div>@enderror
            </div>

            <div class="ed-field">
                <label class="ed-label">Due Date</label>
                <input type="date" name="due_date" value="{{ old('due_date', $delivery->due_date) }}" class="ed-input">
                @error('due_date')<div class="ed-error">{{ $message }}</div>@enderror
            </div>

            <div class="ed-field">
                <label class="ed-label">Status <span class="ed-req">*</span></label>
                <select name="status" class="ed-input" required>
                    @foreach(['pending'=>'Pending','partial'=>'Partial','paid'=>'Paid','overdue'=>'Overdue'] as $k=>$v)
                        <option value="{{ $k }}" {{ old('status', $delivery->status)===$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
                @error('status')<div class="ed-error">{{ $message }}</div>@enderror
            </div>

            <div class="ed-field ed-field-full">
                <label class="ed-label">Notes</label>
                <textarea name="notes" rows="3" class="ed-input ed-textarea">{{ old('notes', $delivery->notes) }}</textarea>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- ITEMS (READ-ONLY)                               --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="ed-card">
        <div class="ed-card-head">
            <div class="ed-card-icon">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="ed-card-header-text">
                <div class="ed-card-title">Delivery Items</div>
                <div class="ed-card-sub">{{ $delivery->items->count() }} product(s) · Read-only</div>
            </div>
        </div>

        <div class="ed-items">
            @foreach($delivery->items as $item)
                <div class="ed-item">
                    <div class="ed-item-info">
                        <div class="ed-item-name">{{ $item->product->name ?? '—' }}</div>
                        @if($item->product->sku ?? null)
                            <div class="ed-item-sku">{{ $item->product->sku }}</div>
                        @endif
                    </div>
                    <div class="ed-item-cell">
                        <div class="ed-item-cell-label">Qty</div>
                        <div class="ed-item-cell-value">{{ $item->quantity_delivered }}</div>
                    </div>
                    <div class="ed-item-cell">
                        <div class="ed-item-cell-label">Price</div>
                        <div class="ed-item-cell-value">&#8369;{{ number_format($item->unit_price, 2) }}</div>
                    </div>
                    <div class="ed-item-cell ed-item-cell-total">
                        <div class="ed-item-cell-label">Subtotal</div>
                        <div class="ed-item-cell-value gold">&#8369;{{ number_format($item->subtotal, 2) }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="ed-total-row">
            <span class="ed-total-label">Grand Total</span>
            <span class="ed-total-value">&#8369;{{ number_format($delivery->items->sum('subtotal'), 2) }}</span>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- ACTIONS                                         --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="ed-actions">
        <a href="{{ route('deliveries.show', $delivery) }}" class="ed-btn ed-btn-ghost">
            Cancel
        </a>
        <button type="submit" class="ed-btn ed-btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
            Update Delivery
        </button>
    </div>
</form>

@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════ */
/* EDIT DELIVERY — CLEAN & PREMIUM                 */
/* ═══════════════════════════════════════════════ */

/* ── BACK ──────────────────────────────────────── */
.ed-back{
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
.ed-back:hover{
    background:rgba(201,169,97,.08);
    border-color:rgba(201,169,97,.28);
    color:#c9a961;
    transform:translateX(-3px);
}
.ed-back svg{transition:transform .2s}
.ed-back:hover svg{transform:translateX(-2px)}

/* ── HERO ──────────────────────────────────────── */
.ed-hero{
    position:relative;
    padding:28px 32px;
    background:#16161d;
    border:1px solid rgba(255,255,255,.06);
    border-radius:18px;
    margin-bottom:18px;
    overflow:hidden;
}
.ed-hero-grid{
    position:absolute;inset:0;
    background-image:
        linear-gradient(rgba(201,169,97,.04) 1px,transparent 1px),
        linear-gradient(90deg,rgba(201,169,97,.04) 1px,transparent 1px);
    background-size:40px 40px;
    mask-image:radial-gradient(circle at 100% 0%,black,transparent 70%);
    -webkit-mask-image:radial-gradient(circle at 100% 0%,black,transparent 70%);
    pointer-events:none;
}
.ed-hero-inner{
    position:relative;z-index:1;
    display:flex;align-items:center;justify-content:space-between;
    gap:24px;flex-wrap:wrap;
}
.ed-hero-left{flex:1;min-width:240px}
.ed-hero-eyebrow{
    display:inline-block;
    font-size:10.5px;font-weight:800;
    color:#c9a961;text-transform:uppercase;letter-spacing:.16em;
    margin-bottom:12px;padding-left:14px;position:relative;
}
.ed-hero-eyebrow::before{
    content:'';position:absolute;left:0;top:50%;
    width:6px;height:1px;background:#c9a961;
    transform:translateY(-50%);
}
.ed-hero-title{
    font-size:30px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.03em;line-height:1.1;
    font-family:ui-monospace,SFMono-Regular,monospace;
    margin:0 0 8px;
}
.ed-hero-sub{
    font-size:13px;color:#8a8792;font-weight:500;
    line-height:1.5;margin:0;
}
.ed-hero-icon{
    width:60px;height:60px;border-radius:15px;
    display:grid;place-items:center;flex-shrink:0;
    background:rgba(201,169,97,.12);
    border:1px solid rgba(201,169,97,.28);
    color:#c9a961;
    box-shadow:0 8px 20px -8px rgba(201,169,97,.4);
}

/* ── FORM WRAP ─────────────────────────────────── */
.ed-form{max-width:920px}

/* ── CARD ──────────────────────────────────────── */
.ed-card{
    background:rgba(34,34,44,.5);
    backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,.06);
    border-radius:16px;
    padding:20px;
    margin-bottom:16px;
}
.ed-card:last-of-type{margin-bottom:0}

.ed-card-head{
    display:flex;align-items:center;gap:12px;
    padding-bottom:16px;margin-bottom:16px;
    border-bottom:1px dashed rgba(255,255,255,.06);
}
.ed-card-icon{
    width:34px;height:34px;border-radius:10px;
    background:rgba(201,169,97,.12);
    border:1px solid rgba(201,169,97,.22);
    color:#c9a961;
    display:grid;place-items:center;flex-shrink:0;
}
.ed-card-icon-muted{
    background:rgba(255,255,255,.05);
    border-color:rgba(255,255,255,.1);
    color:#7a7a85;
}
.ed-card-header-text{flex:1;min-width:0}
.ed-card-title{
    font-size:13.5px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.01em;
}
.ed-card-sub{
    font-size:11px;color:#7a7a85;font-weight:500;
    margin-top:2px;
}

/* ── LOCKED INFO ──────────────────────────────── */
.ed-locked-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
}
.ed-locked-field{
    padding:12px 14px;
    background:rgba(0,0,0,.2);
    border:1px solid rgba(255,255,255,.05);
    border-radius:10px;
}
.ed-locked-label{
    font-size:9.5px;font-weight:800;color:#7a7a85;
    text-transform:uppercase;letter-spacing:.1em;
    margin-bottom:5px;
}
.ed-locked-value{
    font-size:13px;font-weight:700;color:#d4d1cc;
}
.ed-locked-mono{
    font-family:ui-monospace,SFMono-Regular,monospace;
    color:#c9a961;
}

/* ── FORM GRID ─────────────────────────────────── */
.ed-form-grid{
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:16px;
}
.ed-field{display:flex;flex-direction:column}
.ed-field-full{grid-column:1 / -1}

.ed-label{
    display:block;font-size:12px;font-weight:700;
    color:#d4d1cc;margin-bottom:8px;
    letter-spacing:-.005em;
}
.ed-req{color:#ef4444;font-weight:800}

.ed-input{
    width:100%;
    padding:11px 14px;
    background:rgba(20,20,26,.6);
    border:1px solid rgba(255,255,255,.08);
    border-radius:10px;
    color:#f5f3f0;
    font-size:13px;font-family:inherit;
    outline:none;transition:all .18s;
    box-sizing:border-box;
    font-weight:500;
}
.ed-input::placeholder{color:#6b6862}
.ed-input:hover{border-color:rgba(255,255,255,.14)}
.ed-input:focus{
    border-color:#c9a961;
    background:rgba(201,169,97,.03);
    box-shadow:0 0 0 3px rgba(201,169,97,.1);
}
.ed-textarea{resize:vertical;min-height:70px;line-height:1.5}

select.ed-input{
    appearance:none;-webkit-appearance:none;
    background-image:url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%237a7a85' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
    background-repeat:no-repeat;
    background-position:right 12px center;
    background-size:10px;
    padding-right:32px;
    cursor:pointer;
}

.ed-error{
    font-size:11px;color:#f87171;
    margin-top:5px;font-weight:600;
}

/* ── ITEMS (READ-ONLY) ────────────────────────── */
.ed-items{
    display:flex;flex-direction:column;
    gap:0;margin-bottom:14px;
}
.ed-item{
    display:grid;
    grid-template-columns:1fr 80px 110px 130px;
    gap:12px;align-items:center;
    padding:12px 0;
    border-bottom:1px dashed rgba(255,255,255,.05);
}
.ed-item:first-child{padding-top:4px}
.ed-item:last-child{border-bottom:none;padding-bottom:8px}

.ed-item-info{min-width:0}
.ed-item-name{
    font-size:13px;font-weight:800;color:#f5f3f0;
    letter-spacing:-.01em;line-height:1.25;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
    margin-bottom:3px;
}
.ed-item-sku{
    display:inline-block;
    font-family:ui-monospace,monospace;
    font-size:10px;font-weight:700;
    color:#7a7a85;
    padding:1px 6px;
    background:rgba(255,255,255,.04);
    border-radius:4px;
}
.ed-item-cell{text-align:right}
.ed-item-cell-label{
    font-size:9px;font-weight:800;color:#7a7a85;
    text-transform:uppercase;letter-spacing:.1em;
    margin-bottom:4px;
}
.ed-item-cell-value{
    font-size:12.5px;font-weight:700;color:#f5f3f0;
    font-variant-numeric:tabular-nums;
}
.ed-item-cell-total .ed-item-cell-value{
    font-size:13.5px;font-weight:800;
}
.ed-item-cell-value.gold{color:#c9a961}

.ed-total-row{
    display:flex;justify-content:space-between;align-items:center;
    padding-top:16px;
    border-top:2px solid rgba(201,169,97,.2);
}
.ed-total-label{
    font-size:11.5px;font-weight:800;color:#7a7a85;
    text-transform:uppercase;letter-spacing:.1em;
}
.ed-total-value{
    font-size:22px;font-weight:800;
    color:#c9a961;letter-spacing:-.02em;
    font-variant-numeric:tabular-nums;
}

/* ── ACTIONS ───────────────────────────────────── */
.ed-actions{
    display:flex;justify-content:flex-end;
    gap:10px;padding-top:8px;
    margin-top:8px;
}
.ed-btn{
    display:inline-flex;align-items:center;justify-content:center;gap:7px;
    padding:12px 22px;border-radius:10px;
    font-size:13px;font-weight:800;
    text-decoration:none;border:1px solid transparent;
    font-family:inherit;cursor:pointer;
    transition:all .18s cubic-bezier(.4,0,.2,1);
    min-height:46px;
    letter-spacing:.01em;
}
.ed-btn-primary{
    background:linear-gradient(135deg,#c9a961,#a9784a);
    color:#0f0f14;
    box-shadow:0 8px 20px -8px rgba(201,169,97,.55);
}
.ed-btn-primary:hover{
    transform:translateY(-1px);
    box-shadow:0 12px 24px -8px rgba(201,169,97,.7);
    filter:brightness(1.05);
}
.ed-btn-primary:active{transform:translateY(0) scale(.98)}

.ed-btn-ghost{
    background:rgba(255,255,255,.04);
    border-color:rgba(255,255,255,.08);
    color:#d4d1cc;
}
.ed-btn-ghost:hover{
    background:rgba(255,255,255,.08);
    border-color:rgba(255,255,255,.15);
    color:#f5f3f0;
}

/* ── RESPONSIVE ────────────────────────────────── */
@media (max-width:900px){
    .ed-form-grid{grid-template-columns:1fr 1fr}
}
@media (max-width:700px){
    .ed-hero{padding:22px}
    .ed-hero-title{font-size:24px}
    .ed-hero-icon{width:52px;height:52px;border-radius:13px}
    .ed-hero-icon svg{width:22px;height:22px}

    .ed-card{padding:16px}

    .ed-locked-grid{grid-template-columns:1fr}
    .ed-form-grid{grid-template-columns:1fr}

    .ed-item{
        grid-template-columns:1fr 1fr;
        gap:10px;
        padding:14px 0;
    }
    .ed-item-info{grid-column:1 / -1}
    .ed-item-cell{text-align:left}

    .ed-actions{flex-direction:column-reverse}
    .ed-btn{width:100%}
}
@media (max-width:400px){
    .ed-item{grid-template-columns:1fr 1fr 1fr}
    .ed-item-cell-total{grid-column:1 / -1;text-align:right}
}
</style>
@endpush