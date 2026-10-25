<tbody class="divide-y divide-slate-100" id="paymentHistoryBody">
    @forelse($payments as $p)
        <tr class="hover:bg-slate-50 transition-colors odd:bg-white even:bg-slate-50/20 animate-fade-in">
            <td class="px-6 py-3 text-slate-600 font-medium whitespace-nowrap">{{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</td>
            <td class="px-6 py-3">
                <div class="font-mono text-xs font-bold text-slate-900 mb-1">{{ $p->delivery->dr_number ?? 'N/A' }} <span class="text-slate-400 font-normal">({{ $p->store->store_name ?? 'Unknown' }})</span></div>
                @if($p->delivery && $p->delivery->items)
                    <div class="space-y-0.5">
                        @foreach($p->items as $payItem)
                            <div class="text-[10px] text-slate-500 flex items-center gap-1.5">
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                {{ $payItem->deliveryItem->variant->product->name ?? 'Unknown' }} 
                                (&#8369;{{ number_format($payItem->amount_paid, 2) }})
                            </div>
                        @endforeach
                    </div>
                @endif
            </td>
            <td class="px-6 py-3 text-right font-black text-emerald-600">+ &#8369;{{ number_format($p->amount, 2) }}</td>
        </tr>
    @empty
        <tr><td colspan="3" class="text-center py-8 text-slate-500">No payments recorded yet.</td></tr>
    @endforelse
</tbody>