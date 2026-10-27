@extends('layouts.admin')
@section('title', 'Customer Profile')
@section('subtitle', $store->store_name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- 1. HERO PROFILE CARD --}}
    <div class="card p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-brand-100 text-brand-700 flex items-center justify-center font-black text-2xl shadow-sm border border-brand-200">
                {{ strtoupper(substr($store->store_name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h2 class="text-2xl font-black text-ink-900 leading-none">{{ $store->store_name }}</h2>
                    @if($store->customer_type === 'suki')
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-50 text-orange-600 border border-orange-200 uppercase tracking-wider">⭐ Suki VIP</span>
                    @elseif($store->customer_type === 'friend')
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-200 uppercase tracking-wider">👥 Friend</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-ink-100 text-ink-600 border border-ink-200 uppercase tracking-wider">Regular</span>
                    @endif
                </div>
                <div class="text-sm text-ink-500 font-medium flex items-center gap-3">
                    <span><i class="fas fa-map-marker-alt text-ink-400"></i> {{ $store->address ?? 'No address' }}</span>
                    <span class="text-ink-300">|</span>
                    <span><i class="fas fa-phone text-ink-400"></i> {{ $store->contact_number ?? 'No contact' }}</span>
                </div>
            </div>
        </div>

        <div class="shrink-0 w-full md:w-auto">
            <a href="{{ route('deliveries.create') }}?store_id={{ $store->id }}" class="btn btn-primary w-full md:w-auto shadow-md">
                <i class="fas fa-plus-circle"></i> Create Delivery
            </a>
        </div>
    </div>

    {{-- 2. FINANCIAL SUMMARY --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card p-5 border-l-4 border-l-ink-400 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold text-ink-500 uppercase tracking-widest mb-1">Total Billed</div>
                <div class="text-2xl font-black text-ink-900">₱{{ number_format($stats['total_sales'] ?? 0, 2) }}</div>
            </div>
            <i class="fas fa-file-invoice-dollar text-2xl text-ink-200"></i>
        </div>
        <div class="card p-5 border-l-4 border-l-emerald-500 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1">Total Paid</div>
                <div class="text-2xl font-black text-emerald-700">₱{{ number_format($stats['total_paid'] ?? 0, 2) }}</div>
            </div>
            <i class="fas fa-check-circle text-2xl text-emerald-200"></i>
        </div>
        <div class="card p-5 border-l-4 border-l-red-500 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold text-red-500 uppercase tracking-widest mb-1">Balance Due</div>
                <div class="text-2xl font-black text-red-600">₱{{ number_format($stats['outstanding'] ?? 0, 2) }}</div>
            </div>
            <i class="fas fa-hourglass-half text-2xl text-red-200"></i>
        </div>
    </div>

    {{-- 3. ORDER HISTORY --}}
    <div>
        <div class="mb-4 mt-8">
            <h3 class="text-lg font-bold text-ink-900">Order History</h3>
            <p class="text-xs text-ink-500">Click to expand items, or click Pay to record a payment.</p>
        </div>

        <div class="space-y-3">
            @php $receipts = $deliveries ?? $store->deliveryReceipts ?? collect(); @endphp
            
            @forelse($receipts as $dr)
                @php
                    $balance = $dr->balance ?? max(0, $dr->total_amount - ($dr->payments_sum_amount ?? 0));
                @endphp
                
                {{-- CARD PER ORDER --}}
                <div class="card overflow-hidden transition-all duration-200 hover:border-brand-300">
                    
                    {{-- HEADER ROW --}}
                    <div class="px-5 py-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        
                        {{-- Left: Info --}}
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-ink-50 flex items-center justify-center shrink-0 border border-ink-200">
                                <i class="fas fa-shopping-bag text-ink-500"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-ink-900 text-base">{{ $dr->dr_number }}</span>
                                    @if($dr->status === 'delivered' || $dr->status === 'completed')
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 uppercase tracking-wider">Paid</span>
                                    @elseif($dr->status === 'partial')
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-600 border border-amber-200 uppercase tracking-wider">Partial</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-red-50 text-red-600 border border-red-200 uppercase tracking-wider">Unpaid</span>
                                    @endif
                                </div>
                                <div class="text-xs text-ink-500 mt-0.5"><i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($dr->delivery_date)->format('M d, Y') }}</div>
                            </div>
                        </div>

                        {{-- Right: Amounts & Action Buttons --}}
                        <div class="flex items-center gap-6">
                            {{-- Total --}}
                            <div class="text-right">
                                <div class="text-[10px] font-bold text-ink-400 uppercase tracking-widest">Total</div>
                                <div class="text-sm font-bold text-ink-900">₱{{ number_format($dr->total_amount, 2) }}</div>
                            </div>
                            
                            {{-- Balance --}}
                            <div class="text-right">
                                <div class="text-[10px] font-bold text-ink-400 uppercase tracking-widest">Balance</div>
                                <div class="text-sm font-black {{ $balance > 0 ? 'text-red-600' : 'text-emerald-600' }}">₱{{ number_format($balance, 2) }}</div>
                            </div>

                            {{-- PAY BUTTON (Only shows if balance > 0) --}}
                            <div class="w-24 flex justify-end">
                                @if($balance > 0)
                                    {{-- SAFE ROUTE CHECKER: Kung naay payments.create, gamita. Kung wala, i-fallback sa payments.index --}}
                                    @php
                                        $payUrl = '#';
                                        if (\Illuminate\Support\Facades\Route::has('payments.create')) {
                                            $payUrl = route('payments.create', ['delivery_id' => $dr->id]);
                                        } elseif (\Illuminate\Support\Facades\Route::has('payments.index')) {
                                            $payUrl = route('payments.index', ['delivery_id' => $dr->id]);
                                        }
                                    @endphp
                                    
                                    @if($payUrl !== '#')
                                        <a href="{{ $payUrl }}" class="btn btn-primary !py-1.5 !px-3 !text-xs w-full shadow-sm">
                                            <i class="fas fa-money-bill-wave"></i> Pay
                                        </a>
                                    @else
                                        {{-- Fallback kung wala jud ang duha ka route --}}
                                        <button type="button" onclick="alert('Payment route is not configured yet. Please check routes/web.php')" class="btn btn-secondary !py-1.5 !px-3 !text-xs w-full shadow-sm">
                                            <i class="fas fa-exclamation-triangle"></i> Pay
                                        </button>
                                    @endif
                                @else
                                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded border border-emerald-100"><i class="fas fa-check"></i> Settled</span>
                                @endif
                            </div>

                            {{-- Expand Button --}}
                            <button type="button" onclick="toggleAccordion('dr-{{ $dr->id }}')" class="w-8 h-8 rounded-full bg-ink-50 flex items-center justify-center text-ink-500 hover:bg-brand-50 hover:text-brand-600 transition-colors focus:outline-none">
                                <i class="fas fa-chevron-down text-xs transition-transform duration-300 transform" id="icon-dr-{{ $dr->id }}"></i>
                            </button>
                        </div>
                    </div>

                    {{-- EXPANDABLE ITEMS --}}
                    <div id="dr-{{ $dr->id }}" class="hidden border-t border-ink-200 bg-ink-50/50">
                        <div class="p-4">
                            <table class="w-full text-sm bg-white rounded-lg border border-ink-200 overflow-hidden">
                                <thead class="bg-ink-50 border-b border-ink-200">
                                    <tr>
                                        <th class="text-left px-4 py-2 text-[10px] font-bold text-ink-500 uppercase tracking-widest">Product</th>
                                        <th class="text-center px-4 py-2 text-[10px] font-bold text-ink-500 uppercase tracking-widest">Qty</th>
                                        <th class="text-right px-4 py-2 text-[10px] font-bold text-ink-500 uppercase tracking-widest">Price</th>
                                        <th class="text-right px-4 py-2 text-[10px] font-bold text-ink-500 uppercase tracking-widest">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-ink-100">
                                    @foreach($dr->items as $item)
                                        <tr>
                                            <td class="px-4 py-2.5">
                                                <div class="font-bold text-ink-800">{{ $item->variant->product->name ?? 'Unknown' }}</div>
                                                <div class="text-[10px] text-ink-500">{{ $item->variant->name ?? '' }}</div>
                                            </td>
                                            <td class="px-4 py-2.5 text-center font-bold text-ink-700">{{ number_format($item->quantity, 2) }}</td>
                                            <td class="px-4 py-2.5 text-right text-ink-600">₱{{ number_format($item->price, 2) }}</td>
                                            <td class="px-4 py-2.5 text-right font-bold text-ink-900">₱{{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card p-16 text-center border-dashed border-2 border-ink-200 bg-ink-50/50">
                    <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center mb-4 shadow-sm border border-ink-100">
                        <i class="fas fa-box-open text-3xl text-ink-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-ink-900">Wala pay Orders</h3>
                    <p class="text-sm text-ink-500 mt-2 mb-6">Wala pay mga deliveries nga na-record para niining customer.</p>
                    <a href="{{ route('deliveries.create') }}?store_id={{ $store->id }}" class="btn btn-secondary mx-auto">
                        <i class="fas fa-plus"></i> Create First Delivery
                    </a>
                </div>
            @endforelse
        </div>
        
        {{-- PAGINATION --}}
        @if(method_exists($receipts, 'links'))
            <div class="mt-6">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleAccordion(id) {
        const content = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);
        
        if (content.classList.contains('hidden')) {
            // Close all others first
            document.querySelectorAll('[id^="dr-"]').forEach(el => {
                if(el.id !== id && !el.classList.contains('hidden')) {
                    el.classList.add('hidden');
                    document.getElementById('icon-' + el.id).classList.remove('rotate-180');
                }
            });
            
            content.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }
</script>
@endpush