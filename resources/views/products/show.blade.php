@extends('layouts.admin')

@section('title', $product->name)
@section('page-title', 'Product Details')
@section('page-sub', $product->sku . ' · ' . ($product->category->name ?? 'Uncategorized'))

@push('styles')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    @media (max-width: 900px) { .detail-grid { grid-template-columns: 1fr; } }

    .detail-image {
        width: 100%;
        aspect-ratio: 4/3;
        background: linear-gradient(135deg, var(--bg-2), var(--bg-3));
        border-radius: var(--radius-lg);
        display: grid;
        place-items: center;
        overflow: hidden;
        border: 1px solid var(--border);
        margin-bottom: 16px;
    }
    .detail-image img { width: 100%; height: 100%; object-fit: cover; }
    .detail-image .emoji { font-size: 96px; opacity: .85; }

    .spec-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
    }
    .spec-row:last-child { border-bottom: none; }
    .spec-row span:first-child { color: var(--text-muted); }
    .spec-row span:last-child { font-weight: 600; color: var(--text-primary); }

    .price-hero {
        padding: 20px;
        background: linear-gradient(135deg, var(--bg-2), var(--bg-3));
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        margin-bottom: 16px;
    }
    .price-hero .label-sm {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 8px;
    }
    .price-hero .amount {
        font-size: 32px;
        font-weight: 800;
        color: var(--accent);
        letter-spacing: -.03em;
        line-height: 1;
    }
    .price-hero .amount small {
        font-size: 14px;
        color: var(--text-muted);
        font-weight: 500;
    }
</style>
@endpush

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:12px;flex-wrap:wrap;">
    <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm">← Back to Products</a>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm">Edit Product</a>
        <form method="POST" action="{{ route('products.destroy', $product) }}"
              onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm">Delete</button>
        </form>
    </div>
</div>

<div class="detail-grid">
    {{-- LEFT: Image + Name --}}
    <div>
        <div class="detail-image">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
            @else
                <div class="emoji">{{ $product->category->emoji ?? '🫘' }}</div>
            @endif
        </div>

        <div class="card">
            <div style="display:flex;justify-content:space-between;align-items:start;gap:12px;margin-bottom:12px;">
                <div>
                    <div style="font-size:20px;font-weight:800;color:var(--text-primary);letter-spacing:-.02em;">{{ $product->name }}</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:4px;font-family:monospace;">{{ $product->sku }}</div>
                </div>
                @if($product->is_featured)
                    <span class="badge badge-pending">★ Featured</span>
                @endif
            </div>

            @if($product->description)
                <p style="font-size:13px;color:var(--text-secondary);line-height:1.6;">{{ $product->description }}</p>
            @endif

            @if($product->cupping_notes)
                <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border);">
                    <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Cupping Notes</div>
                    <div style="font-size:13px;color:var(--text-secondary);font-style:italic;">"{{ $product->cupping_notes }}"</div>
                </div>
            @endif
        </div>
    </div>

    {{-- RIGHT: Details --}}
    <div>
        {{-- Price --}}
        <div class="price-hero">
            <div class="label-sm">Selling Price</div>
            <div class="amount">
                ₱{{ number_format($product->price, 2) }}
                <small>/ {{ $product->base_unit }}</small>
            </div>
            @if($product->cost_price > 0)
                <div style="margin-top:12px;display:flex;justify-content:space-between;font-size:12px;">
                    <span style="color:var(--text-muted);">Cost: ₱{{ number_format($product->cost_price, 2) }}</span>
                    <span style="color:var(--green);font-weight:700;">+{{ $product->profit_margin }}% margin</span>
                </div>
            @endif
            @if($product->wholesale_price)
                <div style="margin-top:8px;font-size:12px;color:var(--text-muted);">Wholesale: ₱{{ number_format($product->wholesale_price, 2) }}</div>
            @endif
        </div>

        {{-- Stock --}}
        <div class="card">
            <div style="font-size:13px;font-weight:700;color:var(--text-primary);margin-bottom:12px;">Stock Information</div>

            <div class="spec-row">
                <span>Current Stock</span>
                <span>
                    <span class="badge badge-{{ $product->stock_status === 'out' ? 'cancelled' : ($product->stock_status === 'low' ? 'pending' : 'completed') }}">
                        {{ $product->stock }} {{ $product->base_unit }}
                    </span>
                </span>
            </div>
            <div class="spec-row">
                <span>Reorder Level</span>
                <span>{{ $product->reorder_level }} {{ $product->base_unit }}</span>
            </div>
            <div class="spec-row">
                <span>Stock Value</span>
                <span>₱{{ number_format($product->stock * $product->price, 2) }}</span>
            </div>
        </div>

        {{-- Coffee Details --}}
        <div class="card">
            <div style="font-size:13px;font-weight:700;color:var(--text-primary);margin-bottom:12px;">Coffee Details</div>

            @if($product->category)
                <div class="spec-row"><span>Category</span><span>{{ $product->category->name }}</span></div>
            @endif
            @if($product->variety)
                <div class="spec-row"><span>Variety</span><span>{{ $product->variety }}</span></div>
            @endif
            @if($product->origin)
                <div class="spec-row"><span>Origin</span><span>{{ $product->origin }}</span></div>
            @endif
            @if($product->altitude)
                <div class="spec-row"><span>Altitude</span><span>{{ $product->altitude }}</span></div>
            @endif
            @if($product->roast_level)
                <div class="spec-row"><span>Roast Level</span><span>{{ $product->roast_level }}</span></div>
            @endif
            @if($product->process_method)
                <div class="spec-row"><span>Process</span><span>{{ $product->process_method }}</span></div>
            @endif
            @if($product->harvest_year)
                <div class="spec-row"><span>Harvest Year</span><span>{{ $product->harvest_year }}</span></div>
            @endif
            @if($product->weight_grams)
                <div class="spec-row"><span>Weight</span><span>{{ $product->weight_grams }}g</span></div>
            @endif
        </div>

        {{-- Status --}}
        <div class="card" style="margin-bottom:0;">
            <div class="spec-row">
                <span>Status</span>
                <span>
                    @if($product->is_active)
                        <span class="badge badge-completed">Active</span>
                    @else
                        <span class="badge badge-cancelled">Inactive</span>
                    @endif
                </span>
            </div>
            <div class="spec-row">
                <span>Created</span>
                <span>{{ $product->created_at->format('M d, Y') }}</span>
            </div>
            <div class="spec-row">
                <span>Last Updated</span>
                <span>{{ $product->updated_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
</div>

@endsection