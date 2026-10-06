@extends('layouts.admin')
@section('title', 'New Sales Report')
@section('page-title', 'New Sales Report')
@section('page-sub', 'Record store sales report')

@section('content')
<div class="card" style="max-width:800px;">
    <form method="GET" action="{{ route('consignment.reports.create') }}" style="margin-bottom:20px;">
        <label class="label">Select Store First</label>
        <select name="store_id" class="input" onchange="this.form.submit()">
            <option value="">— Select store —</option>
            @foreach($stores as $s)
                <option value="{{ $s->id }}" @selected(request('store_id') == $s->id)>{{ $s->store_name }}</option>
            @endforeach
        </select>
    </form>

    @if($selectedStore && $inventory->count())
        <form method="POST" action="{{ route('consignment.reports.store') }}">
            @csrf
            <input type="hidden" name="store_id" value="{{ $selectedStore->id }}">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div><label class="label">From *</label><input type="date" name="period_from" class="input" required></div>
                <div><label class="label">To *</label><input type="date" name="period_to" class="input" required></div>
            </div>

            <label class="label">Products Sold</label>
            <table style="margin-bottom:16px;">
                <thead><tr><th>Product</th><th>On Hand</th><th>Qty Sold</th></tr></thead>
                <tbody>
                    @foreach($inventory as $inv)
                        <tr>
                            <td>{{ $inv->product->name }}</td>
                            <td>{{ $inv->quantity_on_hand }}</td>
                            <td>
                                <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $inv->product_id }}">
                                <input type="number" name="items[{{ $loop->index }}][quantity_sold]" class="input" min="0" max="{{ $inv->quantity_on_hand }}" value="0" style="width:80px;">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;">Submit Report</button>
        </form>
    @elseif($selectedStore)
        <div style="text-align:center;padding:40px;color:var(--text-muted);">No inventory on hand for this store.</div>
    @else
        <div style="text-align:center;padding:40px;color:var(--text-muted);">Select a store to continue.</div>
    @endif
</div>
@endsection