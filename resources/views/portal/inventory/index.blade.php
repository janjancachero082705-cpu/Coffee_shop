@extends('portal.layouts.app')

@section('title', 'My Inventory')

@section('content')

{{-- HERO STATS --}}
<div class="inv-hero">
    <div class="inv-hero-item">
        <div class="inv-hero-icon gold">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <div class="inv-hero-info">
            <div class="inv-hero-lbl">Total Delivered</div>
            <div class="inv-hero-val">{{ number_format($totalDelivered) }}</div>
        </div>
    </div>
    <div class="inv-hero-item">
        <div class="inv-hero-icon green">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
        </div>
        <div class="inv-hero-info">
            <div class="inv-hero-lbl">On Hand</div>
            <div class="inv-hero-val green">{{ number_format($totalOnHand) }}</div>
        </div>
    </div>
    <div class="inv-hero-item">
        <div class="inv-hero-icon blue">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
        </div>
        <div class="inv-hero-info">
            <div class="inv-hero-lbl">Total Sold</div>
            <div class="inv-hero-val blue">{{ number_format($totalSold) }}</div>
        </div>
    </div>
</div>

{{-- PRODUCTS LIST --}}
@if($inventories->isEmpty())
    <div class="inv-empty">
        <div class="inv-empty-icon">
            <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <div class="inv-empty-title">Wala pay deliveries</div>
        <div class="inv-empty-text">Kung naay ma-deliver, makita dinhi.</div>
    </div>
@else
    <div class="inv-list">
        @foreach($inventories as $inv)
            @php
                $product = $inv->product;
                $initials = strtoupper(substr($product->name ?? 'P', 0, 2));
                $delivered = (int) $inv->quantity_delivered;
                $sold = (int) $inv->quantity_sold;
                $onHand = (int) $inv->quantity_on_hand;
                $soldPct = $delivered > 0 ? min(100, ($sold / $delivered) * 100) : 0;
            @endphp
            <div class="inv-card">
                <div class="inv-card-head">
                    <div class="inv-card-avatar">
                        @if($product && $product->image_url)
                            <img src="{{ $product->image_url }}" alt="">
                        @else
                            {{ $initials }}
                        @endif
                    </div>
                    <div class="inv-card-info">
                        <div class="inv-card-name">{{ $product->name ?? 'Unknown' }}</div>
                        <div class="inv-card-sku">{{ $product->sku ?? '—' }}</div>
                    </div>
                </div>
                <div class="inv-card-stats">
                    <div class="inv-stat">
                        <div class="inv-stat-lbl">Delivered</div>
                        <div class="inv-stat-val">{{ number_format($delivered) }}</div>
                    </div>
                    <div class="inv-stat">
                        <div class="inv-stat-lbl">Sold</div>
                        <div class="inv-stat-val blue">{{ number_format($sold) }}</div>
                    </div>
                    <div class="inv-stat">
                        <div class="inv-stat-lbl">On Hand</div>
                        <div class="inv-stat-val {{ $onHand > 0 ? 'green' : '' }}">{{ number_format($onHand) }}</div>
                    </div>
                </div>
                @if($delivered > 0)
                    <div class="inv-progress">
                        <div class="inv-progress-info">
                            <span>{{ number_format($soldPct, 0) }}% sold</span>
                            <span>{{ number_format($sold) }} / {{ number_format($delivered) }}</span>
                        </div>
                        <div class="inv-progress-track">
                            <div class="inv-progress-fill" style="width: {{ $soldPct }}%;"></div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif

@endsection

@push('styles')
<style>
    .inv-hero { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 18px; }
    .inv-hero-item { display: flex; align-items: center; gap: 10px; padding: 12px; background: linear-gradient(165deg, rgba(30,26,22,0.9), rgba(21,18,15,0.9)); border: 1px solid rgba(255,255,255,0.06); border-radius: 14px; }
    .inv-hero-icon { width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center; flex-shrink: 0; }
    .inv-hero-icon.gold  { background: rgba(201,169,97,0.15); color: #c9a961; }
    .inv-hero-icon.green { background: rgba(34,197,94,0.15); color: #22c55e; }
    .inv-hero-icon.blue  { background: rgba(59,130,246,0.15); color: #3b82f6; }
    .inv-hero-lbl { font-size: 9.5px; font-weight: 800; color: #71717a; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 3px; }
    .inv-hero-val { font-size: 18px; font-weight: 800; color: #fafafa; font-variant-numeric: tabular-nums; line-height: 1; }
    .inv-hero-val.green { color: #22c55e; }
    .inv-hero-val.blue  { color: #3b82f6; }

    .inv-list { display: flex; flex-direction: column; gap: 10px; }
    .inv-card { padding: 14px; background: linear-gradient(165deg, rgba(30,26,22,0.9), rgba(21,18,15,0.9)); border: 1px solid rgba(255,255,255,0.06); border-radius: 14px; }
    .inv-card-head { display: flex; align-items: center; gap: 12px; padding-bottom: 12px; margin-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .inv-card-avatar { width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, rgba(201,169,97,0.25), rgba(201,169,97,0.08)); color: #c9a961; display: grid; place-items: center; font-weight: 800; font-size: 14px; border: 1px solid rgba(201,169,97,0.3); flex-shrink: 0; overflow: hidden; }
    .inv-card-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .inv-card-info { min-width: 0; flex: 1; }
    .inv-card-name { font-size: 14px; font-weight: 800; color: #fafafa; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .inv-card-sku { font-size: 11px; color: #71717a; font-family: ui-monospace, monospace; }
    .inv-card-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
    .inv-stat { padding: 10px 8px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.04); border-radius: 10px; text-align: center; }
    .inv-stat-lbl { font-size: 9px; font-weight: 800; color: #71717a; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px; }
    .inv-stat-val { font-size: 16px; font-weight: 800; color: #fafafa; font-variant-numeric: tabular-nums; }
    .inv-stat-val.green { color: #22c55e; }
    .inv-stat-val.blue  { color: #3b82f6; }

    .inv-progress { margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.04); }
    .inv-progress-info { display: flex; justify-content: space-between; font-size: 10.5px; font-weight: 700; color: #71717a; margin-bottom: 6px; }
    .inv-progress-info span:first-child { color: #c9a961; }
    .inv-progress-track { height: 6px; border-radius: 3px; background: rgba(255,255,255,0.05); overflow: hidden; }
    .inv-progress-fill { height: 100%; background: linear-gradient(90deg, #c9a961, #d4b673); border-radius: 3px; min-width: 3px; }

    .inv-empty { text-align: center; padding: 50px 20px 40px; background: linear-gradient(165deg, rgba(30,26,22,0.9), rgba(21,18,15,0.9)); border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; }
    .inv-empty-icon { width: 72px; height: 72px; margin: 0 auto 16px; border-radius: 20px; background: rgba(201,169,97,0.1); color: rgba(201,169,97,0.6); display: grid; place-items: center; border: 1px solid rgba(201,169,97,0.15); }
    .inv-empty-title { font-size: 17px; font-weight: 800; color: #fafafa; margin-bottom: 6px; }
    .inv-empty-text { font-size: 12.5px; color: #71717a; max-width: 280px; margin: 0 auto; }

    @media (max-width: 480px) {
        .inv-hero { grid-template-columns: 1fr; }
        .inv-stat-val { font-size: 14px; }
    }
</style>
@endpush