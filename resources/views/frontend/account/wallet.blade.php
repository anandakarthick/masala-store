@extends('layouts.app')

@section('title', 'My Wallet')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar -->
        @include('frontend.account.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1">
            <h1 class="text-2xl font-bold mb-6">
                <i class="fas fa-wallet text-green-600 mr-2"></i>My Wallet
            </h1>

            <!-- Wallet Balance Card -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-6 text-white mb-6 shadow-lg">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <p class="text-green-100 text-sm">Available Balance</p>
                        <p class="text-4xl font-bold">₹{{ number_format($stats['balance'], 2) }}</p>
                    </div>
                    <a href="{{ route('account.referrals') }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-users mr-1"></i> Earn More via Referrals
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-gray-500 text-xs">Total Credits</p>
                    <p class="text-xl font-bold text-green-600">₹{{ number_format($stats['total_credits'], 2) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-gray-500 text-xs">Total Debits</p>
                    <p class="text-xl font-bold text-red-600">₹{{ number_format($stats['total_debits'], 2) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-gray-500 text-xs">Referral Earnings</p>
                    <p class="text-xl font-bold text-blue-600">₹{{ number_format($stats['referral_earnings'], 2) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-gray-500 text-xs">Balance</p>
                    <p class="text-xl font-bold text-gray-800">₹{{ number_format($stats['balance'], 2) }}</p>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <h2 class="font-semibold text-gray-800">Transaction History</h2>
                </div>

                @if($transactions->count() > 0)
                    <div class="divide-y">
                        @foreach($transactions as $transaction)
                            <div class="p-4 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $transaction->type === 'credit' ? 'bg-green-100' : 'bg-red-100' }}">
                                            <i class="fas {{ $transaction->type === 'credit' ? 'fa-arrow-down text-green-600' : 'fa-arrow-up text-red-600' }}"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $transaction->description }}</p>
                                            <div class="flex flex-wrap gap-2 mt-1">
                                                <span class="text-xs px-2 py-0.5 rounded-full {{ $transaction->type === 'credit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                                    {{ $transaction->source_label }}
                                                </span>
                                                @if($transaction->order)
                                                    <a href="{{ route('account.orders.show', $transaction->order) }}" class="text-xs text-blue-600 hover:underline">
                                                        Order #{{ $transaction->order->order_number }}
                                                    </a>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">{{ $transaction->created_at->format('d M Y, h:i A') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $transaction->formatted_amount }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Bal: ₹{{ number_format($transaction->balance_after, 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="p-4 border-t">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-receipt text-3xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No Transactions Yet</h3>
                        <p class="text-gray-500 mb-4">Start earning by referring friends!</p>
                        <a href="{{ route('account.referrals') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium">
                            <i class="fas fa-users mr-1"></i> Go to Referrals
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
