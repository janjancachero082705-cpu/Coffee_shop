@extends('layouts.admin')
@section('title', 'System Transactions')
@section('subtitle', 'Audit trail of all activities')

@section('content')
<div class="w-full max-w-screen-2xl mx-auto space-y-4 md:space-y-6">
    <div class="card p-4 md:p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
            <div>
                <h3 class="text-lg font-bold text-ink-900 flex items-center gap-2">
                    <i class="fas fa-list-ul text-brand-500"></i> System Activity Log
                </h3>
                <p class="text-xs text-ink-500 mt-1">Every action made in the system is recorded here.</p>
            </div>
            <form action="{{ route('activity.logs') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                    <select name="module" onchange="this.form.submit()" class="input !py-2 !text-xs w-full sm:w-40 cursor-pointer">
                        <option value="All">All Categories</option>
                        @foreach($modules as $mod)
                            <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                        @endforeach
                    </select>
                    
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description..." class="input !py-2 !text-xs w-full sm:w-48">
                        <button type="submit" class="btn btn-secondary !py-2 !text-xs whitespace-nowrap">Search</button>
                    </div>
                </form>
        </div>

        <div class="overflow-x-auto w-full rounded-xl border border-ink-200" style="-webkit-overflow-scrolling: touch;">
            <table class="table-clean w-full min-w-[700px]">
                <thead class="bg-ink-50 border-b border-ink-200">
                    <tr>
                        <th class="whitespace-nowrap text-[11px] uppercase tracking-wider text-ink-500 w-40">Date & Time</th>
                        <th class="text-[11px] uppercase tracking-wider text-ink-500 w-32">Module</th>
                        <th class="text-[11px] uppercase tracking-wider text-ink-500 w-32">Action</th>
                        <th class="text-[11px] uppercase tracking-wider text-ink-500">Description</th>
                        <th class="text-[11px] uppercase tracking-wider text-ink-500 w-32">User</th>
                        <th class="text-center text-[11px] uppercase tracking-wider text-ink-500 w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100 bg-white">
                    @forelse($logs as $log)
                        <tr class="hover:bg-ink-50/50 transition-colors">
                            <td class="text-ink-500 font-medium text-xs whitespace-nowrap">
                                {{ $log->created_at->format('M d, Y') }} <br>
                                <span class="text-[10px] text-ink-400">{{ $log->created_at->format('h:i A') }}</span>
                            </td>
                            <td>
                                <span class="font-bold text-ink-900 text-xs">{{ $log->module }}</span>
                            </td>
                            <td>
                                @if($log->action === 'Created')
                                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">{{ $log->action }}</span>
                                @elseif($log->action === 'Recorded')
                                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase">{{ $log->action }}</span>
                                @else
                                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase">{{ $log->action }}</span>
                                @endif
                            </td>
                            <td class="text-ink-700 text-xs font-medium">
                                {{ $log->description }}
                            </td>
                            <td class="text-ink-500 text-xs">
                                <i class="fas fa-user-circle mr-1 text-ink-400"></i> {{ $log->user->name ?? 'System' }}
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center">
        <button type="button" onclick="openOverrideModal({{ $log->id }}, '{{ addslashes($log->description) }}', '{{ $log->quantity }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition-all shadow-sm" title="Override Log">
            <i class="fas fa-pen"></i> Override
        </button>
    </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-16">
                                <div class="w-16 h-16 mx-auto bg-ink-50 rounded-full flex items-center justify-center mb-3 border border-ink-100">
                                    <i class="fas fa-clipboard-list text-2xl text-ink-300"></i>
                                </div>
                                <h3 class="text-base font-bold text-ink-800 mb-1">No transactions yet</h3>
                                <p class="text-xs text-ink-500">Start adding data to see the audit trail.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
</div>
<!-- OVERRIDE MODAL -->
<div id="overrideModal" class="fixed inset-0 z-[100] hidden bg-ink-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-pop w-full max-w-md overflow-hidden">
        <form id="overrideForm" method="POST" class="p-6">
            @csrf @method('PUT')
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center"><i class="fas fa-edit"></i></div>
                <div><h3 class="text-lg font-bold text-ink-900">Override Transaction</h3><p class="text-xs text-ink-500">Correct the details below.</p></div>
            </div>
            
            <div class="mb-4">
                <label class="block text-[10px] font-bold text-ink-500 mb-1 uppercase tracking-widest">Description</label>
                <textarea name="description" id="overrideDesc" rows="2" class="input !text-sm w-full" required></textarea>
            </div>

            <div class="mb-4 hidden" id="qtyContainer">
                <label class="block text-[10px] font-bold text-ink-500 mb-1 uppercase tracking-widest">Correct Quantity</label>
                <input type="number" name="quantity" id="overrideQty" step="0.01" class="input font-bold !text-sm w-full">
                <p class="text-[10px] text-amber-600 mt-1"><i class="fas fa-info-circle"></i> Changing this will automatically adjust the actual stock in the inventory.</p>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeOverrideModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Override</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openOverrideModal(id, description, quantity) {
        const modal = document.getElementById('overrideModal');
        const form = document.getElementById('overrideForm');
        const qtyContainer = document.getElementById('qtyContainer');
        
        form.action = `/activity-logs/${id}`;
        document.getElementById('overrideDesc').value = description.replace(' (Overridden)', '');
        
        // Kung naay quantity (Inventory), ipakita ang input field
        if (quantity !== '' && quantity !== null) {
            qtyContainer.classList.remove('hidden');
            document.getElementById('overrideQty').value = quantity;
        } else {
            qtyContainer.classList.add('hidden');
        }
        
        modal.classList.remove('hidden');
    }

    function closeOverrideModal() {
        document.getElementById('overrideModal').classList.add('hidden');
    }
</script>
@endsection