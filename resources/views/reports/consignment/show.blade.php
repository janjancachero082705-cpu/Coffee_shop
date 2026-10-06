@extends('layouts.admin')
@section('title', $report->report_number)
@section('page-title', $report->report_number)
@section('page-sub', $report->store->store_name)

@section('content')
<a href="{{ route('consignment.reports.index') }}" class="btn btn-ghost btn-sm" style="margin-bottom:16px;">&#8592; Back</a>

<div class="card">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
        <div><div style="font-size:11px;color:var(--text-muted);">Period</div><div style="font-weight:700;">{{ $report->period_from->format('M d, Y') }} - {{ $report->period_to->format('M d, Y') }}</div></div>
        <div><div style="font-size:11px;color:var(--text-muted);">Status</div><span class="badge badge-{{ $report->status === 'paid' ? 'completed' : 'pending' }}">{{ ucfirst($report->status) }}</span></div>
    </div>

    <table>
        <thead><tr><th>Product</th><th>Qty Sold</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
        <tbody>
            @foreach($report->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity_sold }}</td>
                    <td>&#8369;{{ number_format($item->unit_price, 2) }}</td>
                    <td><strong>&#8369;{{ number_format($item->subtotal, 2) }}</strong></td>
                </tr>
            @endforeach
            <tr><td colspan="3" style="text-align:right;font-weight:700;">Total:</td><td style="font-weight:800;color:var(--accent-light);font-size:15px;">&#8369;{{ number_format($report->total_sales, 2) }}</td></tr>
        </tbody>
    </table>
</div>
@endsection