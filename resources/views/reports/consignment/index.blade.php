@extends('layouts.admin')
@section('title', 'Sales Reports')
@section('page-title', 'Consignment Sales Reports')
@section('page-sub', 'Reports submitted by stores')

@section('content')
<div class="stats-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
    <div class="stat" style="padding:18px;background:rgba(34,34,44,.6);border:1px solid rgba(255,255,255,.06);border-radius:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px;">Total Reports</div>
        <div style="font-size:22px;font-weight:800;">{{ $stats['total'] }}</div>
    </div>
    <div class="stat" style="padding:18px;background:rgba(34,34,44,.6);border:1px solid rgba(255,255,255,.06);border-radius:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px;">Pending</div>
        <div style="font-size:22px;font-weight:800;color:var(--warning);">{{ $stats['pending'] }}</div>
    </div>
    <div class="stat" style="padding:18px;background:rgba(34,34,44,.6);border:1px solid rgba(255,255,255,.06);border-radius:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px;">Verified</div>
        <div style="font-size:22px;font-weight:800;color:var(--success);">{{ $stats['verified'] }}</div>
    </div>
    <div class="stat" style="padding:18px;background:rgba(34,34,44,.6);border:1px solid rgba(255,255,255,.06);border-radius:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px;">Total Sales</div>
        <div style="font-size:18px;font-weight:800;">&#8369;{{ number_format($stats['total_sales'], 0) }}</div>
    </div>
</div>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
    <div style="font-size:14px;font-weight:700;">All Reports</div>
    <a href="{{ route('consignment.reports.create') }}" class="btn btn-primary" style="height:34px;padding:0 16px;font-size:12px;">+ New Report</a>
</div>

<div class="card">
    @if($reports->isEmpty())
        <div style="text-align:center;padding:60px;color:var(--text-muted);">No reports yet.</div>
    @else
        <table>
            <thead><tr><th>Report #</th><th>Store</th><th>Period</th><th>Qty</th><th>Amount</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach($reports as $report)
                    <tr>
                        <td><strong>{{ $report->report_number }}</strong></td>
                        <td>{{ $report->store->store_name }}</td>
                        <td>{{ $report->period_from->format('M d') }} - {{ $report->period_to->format('M d') }}</td>
                        <td>{{ $report->total_quantity }}</td>
                        <td><strong style="color:var(--accent-light);">&#8369;{{ number_format($report->total_sales, 2) }}</strong></td>
                        <td><span class="badge badge-{{ $report->status === 'paid' ? 'completed' : ($report->status === 'verified' ? 'info' : 'pending') }}">{{ ucfirst($report->status) }}</span></td>
                        <td><a href="{{ route('consignment.reports.show', $report) }}" class="btn btn-ghost btn-sm">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
<div style="margin-top:16px;">{{ $reports->links() }}</div>
@endsection