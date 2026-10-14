@extends('layouts.admin')

@section('title', 'Stores')
@section('subtitle', 'Manage registered stores & pending registrations')

@section('content')

{{-- HERO --}}
<div class="st-hero">
    <div class="st-hero-grid"></div>

    <div class="st-hero-inner">
        <div class="st-hero-left">
            <div class="st-hero-eyebrow">Store Management</div>
            <h1 class="st-hero-title">All Stores</h1>
            <p class="st-hero-sub">Manage active stores and pending registrations in one place</p>
        </div>

        <div class="st-hero-right">
            <div class="st-stat">
                <div class="st-stat-val">{{ $totalCount }}</div>
                <div class="st-stat-lbl">Total</div>
            </div>
            <div class="st-stat">
                <div class="st-stat-val st-stat-val-active">{{ $activeCount }}</div>
                <div class="st-stat-lbl">Active</div>
            </div>
            <div class="st-stat">
                <div class="st-stat-val st-stat-val-pending">{{ $pendingCount }}</div>
                <div class="st-stat-lbl">Pending</div>
            </div>
        </div>
    </div>
</div>

{{-- TOOLBAR --}}
<div class="st-toolbar">
    <div class="st-tabs">
        <a href="{{ route('stores.index', ['tab' => 'all', 'search' => $search]) }}" class="st-tab {{ $tab === 'all' ? 'active' : '' }}">
            All
            <span class="st-tab-count">{{ $totalCount }}</span>
        </a>

        <a href="{{ route('stores.index', ['tab' => 'active', 'search' => $search]) }}" class="st-tab {{ $tab === 'active' ? 'active' : '' }}">
            Active
            <span class="st-tab-count">{{ $activeCount }}</span>
        </a>

        <a href="{{ route('stores.index', ['tab' => 'pending', 'search' => $search]) }}" class="st-tab {{ $tab === 'pending' ? 'active' : '' }}">
            Pending
            @if($pendingCount > 0)
                <span class="st-tab-count st-tab-count-amber">{{ $pendingCount }}</span>
            @endif
        </a>
    </div>

    <form method="GET" action="{{ route('stores.index') }}" class="st-search-form">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="st-search-wrap">
            <svg class="st-search-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search stores…" class="st-search-input">
            @if($search)
                <a href="{{ route('stores.index', ['tab' => $tab]) }}" class="st-search-clear" title="Clear search">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- PENDING REGISTRATIONS --}}
@if(($tab === 'all' || $tab === 'pending') && $pending->count() > 0)
    <div class="st-section">
        <div class="st-section-head">
            <div class="st-section-title">
                Pending Registrations
                <span class="st-section-count">{{ $pending->total() }}</span>
            </div>
            <div class="st-section-hint">Awaiting your review</div>
        </div>

        <div class="st-list">
            @foreach($pending as $reg)
                <a href="{{ route('store-registrations.show', $reg->id) }}" class="st-card st-card-pending">
                    <div class="st-card-left">
                        <div class="st-card-logo st-card-logo-amber">
                            {{ strtoupper(substr($reg->store_name ?? 'ST', 0, 2)) }}
                        </div>
                    </div>

                    <div class="st-card-main">
                        <div class="st-card-title-row">
                            <div class="st-card-name">{{ $reg->store_name }}</div>
                            <span class="st-badge st-badge-pending">
                                <span class="st-badge-dot"></span>
                                Pending
                            </span>
                        </div>
                        <div class="st-card-sub">
                            {{ $reg->owner_name }}
                            @if($reg->email)
                                <span class="st-card-sep">·</span> {{ $reg->email }}
                            @endif
                            @if($reg->contact_number)
                                <span class="st-card-sep">·</span> {{ $reg->contact_number }}
                            @endif
                        </div>
                        @if($reg->address)
                            <div class="st-card-address">{{ $reg->address }}@if($reg->city), {{ $reg->city }}@endif</div>
                        @endif
                    </div>

                    <div class="st-card-right">
                        <div class="st-card-time">{{ $reg->created_at->diffForHumans() }}</div>
                        <div class="st-card-cta">
                            Review
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if($pending->hasPages())
            <div class="st-pagination">{{ $pending->appends(['tab' => $tab, 'search' => $search])->links() }}</div>
        @endif
    </div>
@endif

{{-- REGISTERED STORES --}}
@if($tab === 'all' || $tab === 'active')
    <div class="st-section">
        <div class="st-section-head">
            <div class="st-section-title">
                Registered Stores
                <span class="st-section-count">{{ $stores->total() }}</span>
            </div>
        </div>

        @if($stores->count() === 0)
            <div class="st-empty">
                <div class="st-empty-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                </div>
                <div class="st-empty-title">No stores found</div>
                <div class="st-empty-text">{{ $search ? 'Try a different search term' : 'Wala pang registered stores' }}</div>
            </div>
        @else
            <div class="st-grid">
                @foreach($stores as $store)
                    @php $isActive = ($store->status ?? 'inactive') === 'active'; @endphp
                    <a href="{{ route('stores.show', $store) }}" class="st-card st-card-grid">
                        <div class="st-card-top">
                            <div class="st-card-logo {{ $isActive ? '' : 'st-card-logo-gray' }}">
                                @if($store->logo_url)
                                    <img src="{{ $store->logo_url }}" alt="">
                                @else
                                    {{ strtoupper(substr($store->store_name ?? 'ST', 0, 2)) }}
                                @endif
                            </div>
                            <span class="st-badge {{ $isActive ? 'st-badge-active' : 'st-badge-inactive' }}">
                                <span class="st-badge-dot"></span>
                                {{ ucfirst($store->status ?? 'inactive') }}
                            </span>
                        </div>

                        <div class="st-card-body">
                            <div class="st-card-name">{{ $store->store_name }}</div>
                            <div class="st-card-code">{{ $store->code }}</div>

                            @if($store->owner_name)
                                <div class="st-card-row">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    <span>{{ $store->owner_name }}</span>
                                </div>
                            @endif
                            @if($store->email)
                                <div class="st-card-row">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg>
                                    <span>{{ $store->email }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="st-card-foot">
                            <span class="st-card-time">Since {{ $store->created_at->format('M Y') }}</span>
                            <span class="st-card-arrow">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($stores->hasPages())
                <div class="st-pagination">{{ $stores->appends(['tab' => $tab, 'search' => $search])->links() }}</div>
            @endif
        @endif
    </div>
@endif

@endsection

@push('styles')
<style>
/* HERO */
.st-hero{position:relative;padding:32px;background:#16161d;border:1px solid rgba(255,255,255,.06);border-radius:18px;margin-bottom:20px;overflow:hidden}
.st-hero-grid{position:absolute;inset:0;background-image:linear-gradient(rgba(201,169,97,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(201,169,97,.04) 1px,transparent 1px);background-size:40px 40px;mask-image:radial-gradient(circle at 100% 0%,black,transparent 70%);-webkit-mask-image:radial-gradient(circle at 100% 0%,black,transparent 70%);pointer-events:none}
.st-hero-inner{position:relative;z-index:1;display:flex;align-items:flex-end;justify-content:space-between;gap:32px;flex-wrap:wrap}
.st-hero-left{flex:1;min-width:240px}
.st-hero-eyebrow{display:inline-block;font-size:10.5px;font-weight:800;color:#c9a961;text-transform:uppercase;letter-spacing:.16em;margin-bottom:14px;padding-left:14px;position:relative}
.st-hero-eyebrow::before{content:'';position:absolute;left:0;top:50%;width:6px;height:1px;background:#c9a961;transform:translateY(-50%)}
.st-hero-title{font-size:34px;font-weight:800;color:#f5f3f0;letter-spacing:-.04em;line-height:1.05;margin:0 0 10px}
.st-hero-sub{font-size:13.5px;color:#8a8792;font-weight:500;line-height:1.5;margin:0;max-width:440px}
.st-hero-right{display:flex;align-items:center;gap:0;padding:16px 4px;border-top:1px solid rgba(255,255,255,.06);border-bottom:1px solid rgba(255,255,255,.06)}
.st-stat{padding:0 24px;text-align:center;position:relative}
.st-stat + .st-stat::before{content:'';position:absolute;left:0;top:50%;width:1px;height:28px;background:rgba(255,255,255,.08);transform:translateY(-50%)}
.st-stat-val{font-size:24px;font-weight:800;color:#f5f3f0;letter-spacing:-.03em;line-height:1}
.st-stat-val-active{color:#4ade80}
.st-stat-val-pending{color:#fbbf24}
.st-stat-lbl{font-size:10px;font-weight:700;color:#7a7a85;text-transform:uppercase;letter-spacing:.14em;margin-top:6px}

/* TOOLBAR */
.st-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:20px;flex-wrap:wrap}
.st-tabs{display:flex;gap:4px;padding:4px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:12px;flex:1;min-width:0;overflow-x:auto;scrollbar-width:none}
.st-tabs::-webkit-scrollbar{display:none}
.st-tab{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;background:transparent;border-radius:9px;font-size:12.5px;font-weight:700;color:#8a8792;text-decoration:none;white-space:nowrap;transition:all .18s;flex-shrink:0}
.st-tab:hover{background:rgba(255,255,255,.04);color:#d4d1cc}
.st-tab.active{background:rgba(201,169,97,.12);color:#c9a961;box-shadow:inset 0 0 0 1px rgba(201,169,97,.25)}
.st-tab-count{padding:1px 7px;min-width:20px;background:rgba(255,255,255,.06);color:#8a8792;font-size:10.5px;font-weight:800;border-radius:100px;text-align:center}
.st-tab.active .st-tab-count{background:rgba(201,169,97,.2);color:#c9a961}
.st-tab-count-amber{background:rgba(245,158,11,.15) !important;color:#fbbf24 !important}
.st-search-form{width:100%;max-width:280px}
.st-search-wrap{position:relative;display:flex;align-items:center}
.st-search-icon{position:absolute;left:13px;color:#7a7a85;pointer-events:none;transition:color .2s}
.st-search-input{width:100%;padding:10px 36px 10px 36px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);border-radius:10px;color:#f5f3f0;font-size:13px;font-family:inherit;outline:none;transition:all .18s}
.st-search-input::placeholder{color:#6b6862}
.st-search-input:focus{border-color:rgba(201,169,97,.5);background:rgba(201,169,97,.03);box-shadow:0 0 0 3px rgba(201,169,97,.08)}
.st-search-wrap:focus-within .st-search-icon{color:#c9a961}
.st-search-clear{position:absolute;right:10px;width:22px;height:22px;border-radius:50%;background:rgba(255,255,255,.06);color:#a8a5a0;display:grid;place-items:center;transition:all .15s}
.st-search-clear:hover{background:rgba(239,68,68,.15);color:#fca5a5}

/* SECTION */
.st-section{margin-bottom:32px}
.st-section-head{display:flex;align-items:baseline;justify-content:space-between;gap:12px;margin:0 2px 14px}
.st-section-title{display:inline-flex;align-items:center;gap:10px;font-size:12px;font-weight:800;color:#f5f3f0;text-transform:uppercase;letter-spacing:.12em}
.st-section-count{display:inline-flex;align-items:center;justify-content:center;min-width:24px;height:20px;padding:0 8px;background:rgba(255,255,255,.06);color:#8a8792;font-size:10.5px;font-weight:800;border-radius:100px;letter-spacing:0}
.st-section-hint{font-size:11px;color:#7a7a85;font-weight:600;letter-spacing:.03em}

/* LIST & GRID */
.st-list{display:flex;flex-direction:column;gap:10px}
.st-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:12px}

/* CARD BASE */
.st-card{background:rgba(34,34,44,.45);border:1px solid rgba(255,255,255,.06);border-radius:14px;text-decoration:none;color:inherit;transition:all .22s cubic-bezier(.4,0,.2,1);position:relative;overflow:hidden}
.st-card::after{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:#c9a961;transform:scaleY(0);transform-origin:top;transition:transform .25s cubic-bezier(.4,0,.2,1)}
.st-card:hover{background:rgba(34,34,44,.7);border-color:rgba(255,255,255,.1);transform:translateY(-2px);box-shadow:0 12px 28px -16px rgba(0,0,0,.6)}
.st-card:hover::after{transform:scaleY(1)}

/* PENDING CARD */
.st-card-pending{display:flex;align-items:center;gap:16px;padding:16px 20px}
.st-card-pending::after{background:#f59e0b}
.st-card-pending:hover{border-color:rgba(245,158,11,.25)}
.st-card-pending .st-card-left{flex-shrink:0}
.st-card-pending .st-card-main{flex:1;min-width:0}
.st-card-pending .st-card-right{display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0}
.st-card-title-row{display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap}
.st-card-title-row .st-card-name{margin:0}
.st-card-sub{font-size:12.5px;color:#9a97a3;font-weight:500;line-height:1.5;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.st-card-sep{color:#6b6862;margin:0 2px}
.st-card-address{font-size:11.5px;color:#7a7a85;margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* GRID CARD */
.st-card-grid{display:flex;flex-direction:column;padding:18px}
.st-card-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.st-card-grid .st-card-body{flex:1}
.st-card-grid .st-card-name{font-size:15px;font-weight:800;color:#f5f3f0;letter-spacing:-.02em;line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:4px}
.st-card-code{display:inline-block;font-family:ui-monospace,SFMono-Regular,monospace;font-size:10.5px;font-weight:700;color:#7a7a85;letter-spacing:.05em;padding:2px 6px;background:rgba(255,255,255,.04);border-radius:4px;margin-bottom:14px}
.st-card-row{display:flex;align-items:center;gap:8px;font-size:12px;color:#9a97a3;font-weight:500;margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.st-card-row svg{color:#6b6862;flex-shrink:0}
.st-card-row span{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.st-card-foot{display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding-top:14px;border-top:1px solid rgba(255,255,255,.05)}
.st-card-time{font-size:10.5px;color:#6b6862;font-weight:700;text-transform:uppercase;letter-spacing:.06em}
.st-card-arrow{color:#7a7a85;transition:all .22s}
.st-card:hover .st-card-arrow{color:#c9a961;transform:translateX(3px)}
.st-card-cta{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:800;color:#fbbf24;text-transform:uppercase;letter-spacing:.08em;transition:gap .2s}
.st-card:hover .st-card-cta{gap:8px}

/* LOGO */
.st-card-logo{width:44px;height:44px;border-radius:11px;background:linear-gradient(135deg,#c9a961,#8a5f36);color:#fff;display:grid;place-items:center;font-size:14px;font-weight:800;letter-spacing:-.02em;flex-shrink:0;overflow:hidden}
.st-card-logo-amber{background:linear-gradient(135deg,#f59e0b,#d97706)}
.st-card-logo-gray{background:linear-gradient(135deg,#4b5563,#374151)}
.st-card-logo img{width:100%;height:100%;object-fit:cover}

/* BADGE */
.st-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:100px;font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;flex-shrink:0}
.st-badge-dot{width:5px;height:5px;border-radius:50%;background:currentColor}
.st-badge-active{background:rgba(34,197,94,.12);color:#4ade80;border:1px solid rgba(34,197,94,.22)}
.st-badge-inactive{background:rgba(255,255,255,.05);color:#8a8792;border:1px solid rgba(255,255,255,.08)}
.st-badge-pending{background:rgba(245,158,11,.12);color:#fbbf24;border:1px solid rgba(245,158,11,.25)}
.st-badge-pending .st-badge-dot{animation:stPulse 1.8s ease-in-out infinite}
@keyframes stPulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(1.3);}}

/* EMPTY */
.st-empty{text-align:center;padding:56px 24px;background:rgba(34,34,44,.3);border:1px dashed rgba(255,255,255,.08);border-radius:14px}
.st-empty-icon{width:60px;height:60px;margin:0 auto 14px;border-radius:14px;background:rgba(201,169,97,.06);border:1px solid rgba(201,169,97,.15);color:#c9a961;display:grid;place-items:center}
.st-empty-title{font-size:15px;font-weight:800;color:#f5f3f0;margin-bottom:4px;letter-spacing:-.01em}
.st-empty-text{font-size:12.5px;color:#8a8792;font-weight:500}

/* PAGINATION */
.st-pagination{margin-top:20px}

/* RESPONSIVE */
@media (max-width:900px){
    .st-hero-inner{flex-direction:column;align-items:stretch}
    .st-hero-right{width:100%;justify-content:space-around;padding:14px 0}
    .st-stat{padding:0 12px;flex:1}
}
@media (max-width:768px){
    .st-hero{padding:24px}
    .st-hero-title{font-size:26px}
    .st-hero-sub{font-size:12.5px}
    .st-stat-val{font-size:20px}
    .st-stat-lbl{font-size:9.5px}
    .st-toolbar{flex-direction:column;align-items:stretch}
    .st-tabs{width:100%}
    .st-tab{flex:1;justify-content:center;padding:9px 12px;font-size:12px}
    .st-search-form{max-width:100%}
    .st-card-pending{flex-direction:column;align-items:flex-start;padding:16px}
    .st-card-pending .st-card-right{flex-direction:row;justify-content:space-between;align-items:center;width:100%;gap:12px}
    .st-grid{grid-template-columns:1fr}
}
@media (max-width:480px){
    .st-hero{padding:20px}
    .st-hero-title{font-size:22px}
    .st-hero-right{flex-wrap:wrap;gap:0}
    .st-stat{flex:1;min-width:70px}
    .st-stat + .st-stat::before{height:24px}
    .st-stat-val{font-size:18px}
    .st-section-title{font-size:11px}
    .st-tab{padding:8px 10px;font-size:11.5px}
    .st-tab-count{font-size:10px;padding:1px 6px;min-width:18px}
}
</style>
@endpush