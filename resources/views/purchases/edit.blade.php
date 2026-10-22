@extends('layouts.admin')
@section('title','Update Payment')
@section('subtitle', $purchase->purchase_number)
@section('content')
<form method="POST" action="{{ route('purchases.update', $purchase) }}" class="space-y-5 max-w-2xl">
    @csrf @method('PUT')
    <div class="bg-white rounded-2xl shadow-card p-6 space-y-4">
        <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Payment Status</label>
            <select name="payment_status" class="w-full px-4 py-2 border border-coffee-200 rounded-lg">
                @foreach(['unpaid','partial','paid'] as $s)
                    <option value="{{ $s }}" {{ $purchase->payment_status === $s ? 'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Amount Paid</label>
            <input type="number" name="amount_paid" step="0.01" min="0" value="{{ $purchase->amount_paid }}" class="w-full px-4 py-2 border border-coffee-200 rounded-lg">
            <p class="text-xs text-coffee-500 mt-1">Total: ₱{{ number_format($purchase->total_amount,2) }}</p>
        </div>
        <div><label class="block text-sm font-semibold text-coffee-800 mb-1">Notes</label>
            <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-coffee-200 rounded-lg">{{ $purchase->notes }}</textarea>
        </div>
    </div>
    <div class="flex justify-end gap-3">
        <a href="{{ route('purchases.index') }}" class="px-6 py-2 bg-coffee-100 text-coffee-700 rounded-lg font-semibold">Cancel</a>
        <button class="px-6 py-2 bg-gradient-to-r from-gold-600 to-gold-800 text-white font-bold rounded-lg">Update</button>
    </div>
</form>
@endsection