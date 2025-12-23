@extends('layouts.admin')
@section('title', 'User Wallet - ' . $user->name)
@section('page_title', 'Wallet: ' . $user->name)

@section('content')
<!-- User Info Card -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                <span class="text-2xl font-bold text-green-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-gray-500">{{ $user->email }}</p>
                <p class="text-sm text-gray-400">Referral Code: <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $user->referral_code }}</span></p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-gray-500 text-sm">Current Balance</p>
            <p class="text-3xl font-bold text-green-600">₹{{ number_format($user->wallet_balance, 2) }}</p>
        </div>
    </div>
</div>

<!-- Adjust Balance Form -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Adjust Wallet Balance</h3>
    <form action="{{ route('admin.referrals.adjust-wallet', $user) }}" method="POST" class="flex flex-wrap gap-4 items-end">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <select name="type" required class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="credit">Credit (+)</option>
                <option value="debit">Debit (-)</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₹)</label>
            <input type="number" name="amount" step="0.01" min="0.01" required
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500 w-32">
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <input type="text" name="description" required placeholder="Reason for adjustment..."
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-save mr-1"></i> Apply
        </button>
    </form>
</div>

<!-- Transaction History -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-4 border-b bg-gray-50">
        <h3 class="font-semibold text-gray-800">Transaction History</h3>
    </div>
    
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance After</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($transactions as $transaction)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $transaction->created_at->format('d M Y, h:i A') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full {{ $transaction->type === 'credit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($transaction->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap font-medium {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $transaction->type === 'credit' ? '+' : '-' }}₹{{ number_format($transaction->amount, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-600">
                            {{ $transaction->source_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                        {{ $transaction->description }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        ₹{{ number_format($transaction->balance_after, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($transaction->order)
                            <a href="{{ route('admin.orders.show', $transaction->order) }}" class="text-blue-600 hover:underline">
                                Order #{{ $transaction->order->order_number }}
                            </a>
                        @endif
                        @if($transaction->referenceUser)
                            <span class="text-gray-500">
                                {{ $transaction->referenceUser->name }}
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-receipt text-4xl mb-3 text-gray-300"></i>
                        <p>No transactions found.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($transactions->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $transactions->links() }}
        </div>
    @endif
</div>

<div class="mt-4">
    <a href="{{ route('admin.referrals.index') }}" class="text-gray-600 hover:text-gray-800">
        <i class="fas fa-arrow-left mr-1"></i> Back to Referrals
    </a>
</div>
@endsection
