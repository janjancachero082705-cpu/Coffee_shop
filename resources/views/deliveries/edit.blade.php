@extends('layouts.admin')

@section('title', 'Edit Delivery')
@section('subtitle', $delivery->dr_number)

@section('actions')
    <a href="{{ route('deliveries.show', $delivery) }}" class="btn btn-ghost btn-sm">← Back</a>
@endsection

@section('content')

<form method="POST" action="{{ route('deliveries.update', $delivery) }}" style="max-width: 900px;">
    @csrf @method('PUT')

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Delivery Information</div>
                <div class="card-sub">Update dates, status, and notes</div>
            </div>
        </div>

        <div class="form-grid">
            <div class="field">
                <label class="label">Store</label>
                <input type="text" value="{{ $delivery->store->store_name ?? '—' }}" class="input" disabled style="opacity:0.6;">
                <div class="hint">Store cannot be changed after creation</div>
            </div>

            <div class="field">
                <label class="label">DR Number</label>
                <input type="text" value="{{ $delivery->dr_number }}" class="input" disabled style="opacity:0.6; font-family:ui-monospace;">
            </div>

            <div class="field">
                <label class="label">Delivery Date <span class="req">*</span></label>
                <input type="date" name="delivery_date" value="{{ old('delivery_date', $delivery->delivery_date) }}" class="input" required>
                @error('delivery_date')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label class="label">Due Date</label>
                <input type="date" name="due_date" value="{{ old('due_date', $delivery->due_date) }}" class="input">
                @error('due_date')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label class="label">Status <span class="req">*</span></label>
                <select name="status" class="input" required>
                    @foreach(['pending'=>'Pending','partial'=>'Partial','paid'=>'Paid','overdue'=>'Overdue'] as $k=>$v)
                        <option value="{{ $k }}" {{ old('status', $delivery->status)===$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
                @error('status')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field field-full">
                <label class="label">Notes</label>
                <textarea name="notes" rows="3" class="input">{{ old('notes', $delivery->notes) }}</textarea>
            </div>
        </div>
    </div>

    {{-- Read-only items summary --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Items (Read-only)</div>
                <div class="card-sub">Items cannot be edited after delivery</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align:right;">Qty</th>
                    <th style="text-align:right;">Unit Price</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($delivery->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? '—' }}</td>
                        <td style="text-align:right;">{{ $item->quantity_delivered }}</td>
                        <td style="text-align:right;">₱{{ number_format($item->unit_price, 2) }}</td>
                        <td style="text-align:right; font-weight:700; color:#c9a961;">₱{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="form-actions">
        <a href="{{ route('deliveries.show', $delivery) }}" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
            Update Delivery
        </button>
    </div>
</form>

@endsection

@push('styles')
<style>
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .field-full { grid-column: 1 / -1; }
    .field { display: flex; flex-direction: column; }
    .req { color: #ef4444; font-weight: 700; }
    .field-error { font-size: 11px; color: #ef4444; margin-top: 5px; font-weight: 500; }
    .hint { font-size: 11px; color: var(--text-muted); margin-top: 5px; font-style: italic; }
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 8px;
    }
    @media (max-width: 700px) { .form-grid { grid-template-columns: 1fr; } }
</style>
@endpush